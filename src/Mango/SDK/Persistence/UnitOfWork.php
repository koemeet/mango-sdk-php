<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Persistence;

use Doctrine\Common\Inflector\Inflector;
use Mango\SDK\Proxy\ProxyFactory;
use Mango\SDK\Registry\ResourceRegistry;
use Mango\SDK\Utils\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class UnitOfWork
{
    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var ResourceRegistry
     */
    protected $resourceRegistry;

    /**
     * Holds all existing references.
     *
     * @var array
     */
    protected $identityMap = [];

    public function __construct(ResourceRegistry $resourceRegistry, ProxyFactory $proxyFactory)
    {
        $this->proxyFactory = $proxyFactory;
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * Register an object in the identity map.
     *
     * @param object $object
     *
     * @return bool
     */
    public function addToIdentityMap($object)
    {
        $className = ClassUtils::getRealClass($object);
        $identifier = $object->getId();

        if (!$identifier) {
            return false;
        }

        if ($this->isInIdentityMap($object)) {
            return false;
        }

        $this->identityMap[$className][$identifier] = $object;

        return true;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function isInIdentityMap($object)
    {
        $className = ClassUtils::getRealClass($object);
        $identifier = $object->getId();

        return isset($this->identityMap[$className][$identifier]);
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function removeFromIdentityMap($object)
    {
        $className = ClassUtils::getRealClass($object);
        $identifier = $object->getId();

        if ($this->isInIdentityMap($object)) {
            unset($this->identityMap[$className][$identifier]);

            return true;
        }

        return false;
    }

    /**
     * @param $className
     * @param $identifier
     *
     * @return mixed
     */
    public function getFromIdentityMap($className, $identifier)
    {
        return isset($this->identityMap[$className][$identifier]) ? $this->identityMap[$className][$identifier] : null;
    }

    /**
     * @param string $className
     * @param array $data
     *
     * @return object
     */
    public function createObject($className, array $data)
    {
        $identifier = $data['id'];

        if (!$object = $this->getFromIdentityMap($className, $identifier)) {
            $object = new $className;

            $idProperty = new \ReflectionProperty($className, 'id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($object, $data['id']);

            $this->addToIdentityMap($object);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($data['attributes'] as $field => $value) {
            $field = Inflector::camelize($field);

            if ($propertyAccessor->isWritable($object, $field)) {
                $propertyAccessor->setValue($object, $field, $value);
            }
        }

        foreach ($data['relationships'] as $field => $relationship) {
            $field = Inflector::camelize($field);

            if (!isset($relationship['data'])) {
                continue;
            }

            $relationship = $relationship['data'];

            // skip has-many relationships for now. TODO
            if (!isset($relationship['type'])) {
                continue;
            }

            if ($propertyAccessor->isWritable($object, $field)) {
                $proxyClass = $this->resourceRegistry->get($relationship['type']);

                if (!$proxyClass) {
                    continue;
                }

                if (!$proxy = $this->getFromIdentityMap($proxyClass, $relationship['id'])) {
                    $proxy = $this->proxyFactory->getProxy($proxyClass, $relationship['id']);
                    $this->addToIdentityMap($proxy);

                }

                $propertyAccessor->setValue($object, $field, $proxy);
            }
        }

        return $object;
    }
}
