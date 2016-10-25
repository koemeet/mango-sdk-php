<?php

require_once "vendor/autoload.php";

$registry = new \Mango\SDK\Registry\ResourceRegistry();
$registry->add('shipments', \Mango\SDK\Model\Shipment::class);
$registry->add('shipping-methods', \Mango\SDK\Model\ShipmentMethod::class);
$registry->add('products', \Mango\SDK\Model\Product::class);
$registry->add('channels', \Mango\SDK\Model\Channel::class);

$refreshToken = '';

$client = new \Mango\SDK\Client($registry, 'postnl', 'postnl', 'MmJlYjQxN2Y4NWQ2MjU5YzljZGE3ZWEyNmIwNDdkMzM2MmYzMGFkM2M1YjE4ODJmMGQ1NDk0N2ZiODk1OTdmMw');

/** @var \Mango\SDK\Model\Product $product */
$product = $client->find('products', 1, [
    'page' => 1,
]);

echo 'Name: '.$product->getName().PHP_EOL;

print_r(count($product->getChannels()));

print('request count: '.$client->getRequestCount());

echo PHP_EOL;

die;
