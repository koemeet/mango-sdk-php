<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK\Utils;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Hmac
{
    /**
     * @param string $secret
     *
     * @return bool
     */
    public static function verify($secret)
    {
        if (!isset($_GET['hmac'])) {
            return false;
        }

        $data = [];

        if (isset($_GET['reference'])) {
            $data['reference'] = $_GET['reference'];
        }

        // convert array to string representation
        $data = http_build_query($data);

        // for post requests we prepend the input stream
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $input = file_get_contents('php://input');
            $data = $input.$data;
        }

        $hmac = hash_hmac('sha256', $data, $secret);

        return $hmac === $_GET['hmac'];
    }
}
