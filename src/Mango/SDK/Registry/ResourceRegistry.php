<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Registry;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ResourceRegistry
{
    /**
     * @var array
     */
    private $mappings = [];

    /**
     * @param $type
     * @param $className
     */
    public function add($type, $className)
    {
        $this->mappings[$type] = $className;
    }

    /**
     * @param string $type
     *
     * @return null|string
     */
    public function get($type)
    {
        return isset($this->mappings[$type]) ? $this->mappings[$type] : null;
    }

    /**
     * @param $className
     *
     * @return mixed
     */
    public function getType($className)
    {
        return array_search($className, $this->mappings);
    }
}
