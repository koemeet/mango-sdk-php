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
class Product
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Channel[]
     */
    protected $channels;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Channel[]
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param Channel[] $channels
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
    }
}
