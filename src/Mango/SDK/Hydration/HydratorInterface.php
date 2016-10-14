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

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface HydratorInterface
{
    /**
     * @param array $data
     *
     * @return mixed
     */
    public function hydrateAll(array $data);

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function hydrateSingle(array $data);
}
