<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Proxy;

use Mango\SDK\Client;
use Mango\SDK\Registry\ResourceRegistry;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
final class ProxyFactory
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ResourceRegistry
     */
    private $registry;

    public function __construct(Client $client, ResourceRegistry $registry)
    {
        $this->client = $client;
        $this->registry = $registry;
    }

    /**
     * @param $className
     * @param $identifier
     *
     * @return GhostObjectInterface
     */
    public function getProxy($className, $identifier)
    {
        $factory = new LazyLoadingGhostFactory();

        $initializer = function(
            GhostObjectInterface $ghostObject,
            string $method,
            array $parameters,
            &$initializer,
            array $properties
        ) use ($className, $identifier) {
            $initializer = null;

            $this->client->find($this->registry->getType($className), $identifier);

            return true;
        };

        $proxy = $factory->createProxy($className, $initializer, [
            'skippedProperties' => [
                "\0*\0id",
            ],
        ]);

        $idProperty = new \ReflectionProperty($className, 'id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($proxy, $identifier);

        return $proxy;
    }
}
