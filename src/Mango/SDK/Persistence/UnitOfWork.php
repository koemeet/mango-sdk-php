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
        return $this->identityMap[$className][$identifier];
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

        if (isset($this->identityMap[$className][$identifier])) {
            $object = $this->getFromIdentityMap($className, $identifier);
        } else {
            $object = new $className;

            // set id of object
            $refl = new \ReflectionClass(get_class($object));
            $prop = $refl->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($object, $data['id']);

            $this->addToIdentityMap($object);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($data['attributes'] as $field => $value) {
            if ($propertyAccessor->isWritable($object, $field)) {
                $propertyAccessor->setValue($object, $field, $value);
            }
        }

        foreach ($data['relationships'] as $field => $relationship) {
            $relationship = $relationship['data'];

            if (!isset($relationship['type'])) {
                continue;
            }

            if ($propertyAccessor->isWritable($object, $field)) {
                $proxyClass = $this->resourceRegistry->get($relationship['type']);
                if ($proxyClass) {
                    $proxy = $this->proxyFactory->getProxy($proxyClass, $relationship['id']);
                    $propertyAccessor->setValue($object, $field, $proxy);

                    $this->addToIdentityMap($proxy);
                }
            }
        }

        return $object;
    }
}
