<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK;

use Doctrine\Common\Inflector\Inflector;
use GuzzleHttp\Exception\ClientException;
use Mango\SDK\Hydration\HydratorInterface;
use Mango\SDK\Hydration\ObjectHydrator;
use Mango\SDK\Persistence\UnitOfWork;
use Mango\SDK\Proxy\ProxyFactory;
use Mango\SDK\Registry\ResourceRegistry;
use Mango\SDK\Storage\TokenStorageInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Client
{
    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var int
     */
    protected $requestCount = 0;

    /**
     * @param ResourceRegistry $registry
     * @param TokenStorageInterface $tokenStorage
     * @param $clientId
     * @param $clientSecret
     */
    public function __construct(
        ResourceRegistry $registry,
        TokenStorageInterface $tokenStorage,
        $clientId,
        $clientSecret
    ) {
        $this->unitOfWork = new UnitOfWork($registry, new ProxyFactory($this, $registry));
        $this->hydrator = new ObjectHydrator($this->unitOfWork, $registry);

        $this->http = new \GuzzleHttp\Client([
            'base_uri' => 'http://mango.docker/'
        ]);

        $this->tokenStorage = $tokenStorage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $code
     * @param string $redirectUri
     *
     * @return array
     */
    public function exchangeCodeForAccessToken($code, $redirectUri)
    {
        $response = $this->http->request('POST', '/oauth/v2/token', [
            'json' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $redirectUri,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    /**
     * @param $resource
     * @param array $query
     *
     * @return array|mixed
     */
    public function query($resource, array $query = [])
    {
        $data = $this->doRequest('GET', Inflector::pluralize($resource), $query);

        // hydrate all objects
        $objects = $this->hydrator->hydrateAll($data);

        return $objects;
    }

    /**
     * @param $resource
     * @param null $id
     * @param array $options
     *
     * @return mixed|object
     */
    public function find($resource, $id, array $options = null)
    {
        $data = $this->doRequest('GET', Inflector::pluralize($resource).'/'.$id, $options);

        // hydrate all objects
        $objects = $this->hydrator->hydrateSingle($data);

        return $objects;
    }

    /**
     * @param $resource
     * @param array $data
     *
     * @return mixed|object
     */
    public function create($resource, array $data)
    {
        $data = $this->doRequest('POST', Inflector::pluralize($resource), null, $data);

        return $this->hydrator->hydrateSingle($data);
    }

    /**
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $options
     * @param array $data
     *
     * @return mixed
     */
    protected function doRequest($method, $path, array $options = null, array $data = null)
    {
        $query = [];

        if (isset($options['include'])) {
            $include = $options['include'];
            if (is_array($include)) {
                $include = implode(',', $include);
            }
            $query['include'] = $include;
            unset($options['include']);
        }

        if ($options) {
            $query = array_replace($query, $options);
        }

        try {
            $options = [
                'query' => $query,
                'headers' => [
                    'Authorization' => 'Bearer '.$this->tokenStorage->getAccessToken(),
                ],
            ];

            if (null !== $data) {
                $options['json'] = $data;
            }

            $response = $this->http->request($method, '/api/'.trim($path, '/'), $options);
        } catch (ClientException $e) {
            if (401 === $e->getCode()) {
                $this->requestNewAccessToken();

                return $this->doRequest($method, $path, $options);
            }

            throw $e;
        }

        $this->requestCount++;

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Requests a new access token.
     */
    protected function requestNewAccessToken()
    {
        $response = $this->http->request('POST', '/oauth/v2/token', [
            'json' => [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->tokenStorage->getRefreshToken(),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->tokenStorage->store($data['access_token'], $data['refresh_token']);
    }
}
