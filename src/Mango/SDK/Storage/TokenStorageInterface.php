<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Storage;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface TokenStorageInterface
{
    /**
     * @param string $accessToken
     * @param string $refreshToken
     */
    public function store($accessToken, $refreshToken);

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @return string
     */
    public function getRefreshToken();
}
