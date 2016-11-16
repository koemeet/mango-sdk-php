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
class InstalledApp
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var App
     */
    protected $app;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param App $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }
}
