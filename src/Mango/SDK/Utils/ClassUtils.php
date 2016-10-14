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

use ProxyManager\Proxy\GhostObjectInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ClassUtils
{
    /**
     * @param $object
     *
     * @return string
     */
    public static function getRealClass($object)
    {
        $refl = new \ReflectionClass(get_class($object));

        if ($object instanceof GhostObjectInterface) {
            return $refl->getParentClass()->getName();
        }

        return get_class($object);
    }
}
