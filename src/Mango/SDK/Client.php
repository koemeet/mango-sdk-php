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

use Mango\SDK\Hydration\HydratorInterface;
use Mango\SDK\Hydration\ObjectHydrator;
use Mango\SDK\Persistence\UnitOfWork;
use Mango\SDK\Proxy\ProxyFactory;
use Mango\SDK\Registry\ResourceRegistry;

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
     * @var int
     */
    protected $requestCount = 0;

    public function __construct(ResourceRegistry $registry)
    {
        $this->unitOfWork = new UnitOfWork($registry, new ProxyFactory($this, $registry));
        $this->hydrator = new ObjectHydrator($this->unitOfWork, $registry);

        $this->http = new \GuzzleHttp\Client([
            'base_uri' => 'http://mango.docker/api/',
            'headers' => [
                'Authorization' => 'Bearer YjVkMGRiNDUxMzY3NWU0MWYyNzcyNGQ0MGRkYTBkYWNiOTYxMzA5YmQxY2Y1MWYwMTdlZGEwYzFkNDVkY2U1NQ',
            ],
        ]);
    }

    /**
     * @param $resource
     * @param array $query
     *
     * @return array|mixed
     */
    public function query($resource, array $query)
    {
        $data = $this->doRequest('GET', $resource);

        // hydrate all objects
        $objects = $this->hydrator->hydrateAll($data['data']);

        return $objects;
    }

    /**
     * @param $resource
     * @param null $id
     *
     * @return mixed|object
     */
    public function find($resource, $id = null)
    {
        $data = $this->doRequest('GET', $resource.'/'.$id);

        // hydrate all objects
        $objects = $this->hydrator->hydrateSingle($data['data']);

        return $objects;
    }

    /**
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }

    /**
     * @param $method
     * @param $path
     *
     * @return mixed
     */
    protected function doRequest($method, $path)
    {
        $response = $this->http->request($method, $path);

        $this->requestCount++;

        return json_decode($response->getBody()->getContents(), true);
    }
}
