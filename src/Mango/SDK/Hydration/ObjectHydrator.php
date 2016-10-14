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
        $objects = [];

        foreach ($data as $item) {
            $objects[] = $this->getObject($item['type'], $item);
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateSingle(array $data)
    {
        return $this->getObject($data['type'], $data);
    }

    /**
     * @param $type
     * @param array $data
     *
     * @return object
     */
    private function getObject($type, array $data)
    {
        return $this->unitOfWork->createObject($this->resourceRegistry->get($type), $data);
    }
}
