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
    protected $currentLocale;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Channel[]
     */
    protected $channels;

    /**
     * @var ProductVariant
     */
    protected $masterVariant;

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
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * @param string $currentLocale
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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

    /**
     * @return ProductVariant
     */
    public function getMasterVariant()
    {
        return $this->masterVariant;
    }

    /**
     * @param ProductVariant $masterVariant
     */
    public function setMasterVariant($masterVariant)
    {
        $this->masterVariant = $masterVariant;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->masterVariant->getPrice();
    }
}
