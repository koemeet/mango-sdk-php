<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Model;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Shipment
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var ShipmentMethod
     */
    protected $method;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return ShipmentMethod
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param ShipmentMethod $method
     */
    public function setMethod(ShipmentMethod $method)
    {
        $this->method = $method;
    }
}
