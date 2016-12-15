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

use Mango\SDK\Model\Address;
use Mango\SDK\Model\App;
use Mango\SDK\Model\Channel;
use Mango\SDK\Model\Customer;
use Mango\SDK\Model\InstalledApp;
use Mango\SDK\Model\Order;
use Mango\SDK\Model\Product;
use Mango\SDK\Model\ProductVariant;
use Mango\SDK\Model\Shipment;
use Mango\SDK\Model\ShipmentMethod;
use Mango\SDK\Model\User;
use Mango\SDK\Model\Workspace;
use Mango\SDK\Registry\ResourceRegistry;
use Mango\SDK\Storage\TokenStorageInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ClientBuilder
{
    /**
     * @param string $baseUri
     * @param TokenStorageInterface $tokenStorage
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return Client
     */
    public static function create($baseUri, TokenStorageInterface $tokenStorage, $clientId, $clientSecret)
    {
        $registry = new ResourceRegistry();
        $registry->add('workspaces', Workspace::class);
        $registry->add('customers', Customer::class);
        $registry->add('users', User::class);
        $registry->add('apps', App::class);
        $registry->add('installed-apps', InstalledApp::class);
        $registry->add('shipments', Shipment::class);
        $registry->add('shipping-methods', ShipmentMethod::class);
        $registry->add('products', Product::class);
        $registry->add('product-variants', ProductVariant::class);
        $registry->add('channels', Channel::class);
        $registry->add('orders', Order::class);
        $registry->add('addresses', Address::class);


        return new Client($baseUri, $registry, $tokenStorage, $clientId, $clientSecret);
    }
}
