<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Hydration;

use Mango\SDK\Persistence\UnitOfWork;
use Mango\SDK\Registry\ResourceRegistry;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ObjectHydrator implements HydratorInterface
{
    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @var ResourceRegistry
     */
    protected $resourceRegistry;

    public function __construct(UnitOfWork $unitOfWork, ResourceRegistry $resourceRegistry)
    {
        $this->unitOfWork = $unitOfWork;
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateAll(array $data)
    {
        $this->processIncluded($data);

        $objects = [];

        foreach ($data['data'] as $item) {
            $objects[] = $this->getObject($item['type'], $item);
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateSingle(array $data)
    {
        $this->processIncluded($data);

        return $this->getObject($data['data']['type'], $data['data']);
    }

    /**
     * @param array $data
     */
    private function processIncluded(array $data)
    {
        if (isset($data['included'])) {
            $included = $data['included'];

            foreach ($included as $include) {
                $this->getObject($include['type'], $include);
            }
        }
    }

    /**
     * @param $type
     * @param array $data
     *
     * @return object
     */
    private function getObject($type, array $data)
    {
        $className = $this->resourceRegistry->get($type);

        if (!$className) {
            throw new \RuntimeException(sprintf(
                'Trying to create object for type "%s", but it is not a registered resource.',
                $type
            ));
        }

        return $this->unitOfWork->createObject($className, $data);
    }
}
