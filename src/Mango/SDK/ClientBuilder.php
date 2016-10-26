<?php

/*
 * This file is part of the Shopblender package.
 *
 * (c) Steffen Brem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mango\SDK;

use Mango\SDK\Model\Channel;
use Mango\SDK\Model\Product;
use Mango\SDK\Model\ProductVariant;
use Mango\SDK\Model\Shipment;
use Mango\SDK\Model\ShipmentMethod;
use Mango\SDK\Registry\ResourceRegistry;
use Mango\SDK\Storage\TokenStorageInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ClientBuilder
{
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return Client
     */
    public static function create(TokenStorageInterface $tokenStorage, $clientId, $clientSecret)
    {
        $registry = new ResourceRegistry();
        $registry->add('shipments', Shipment::class);
        $registry->add('shipping-methods', ShipmentMethod::class);
        $registry->add('products', Product::class);
        $registry->add('product-variants', ProductVariant::class);
        $registry->add('channels', Channel::class);

        return new Client($registry, $tokenStorage, $clientId, $clientSecret);
    }
}
