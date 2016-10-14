<?php

require_once "vendor/autoload.php";

$registry = new \Mango\SDK\Registry\ResourceRegistry();
$registry->add('shipments', \Mango\SDK\Model\Shipment::class);
$registry->add('shipping-methods', \Mango\SDK\Model\ShipmentMethod::class);

$client = new \Mango\SDK\Client($registry);

/** @var \Mango\SDK\Model\Shipment $shipment */
$shipment = $client->find('shipments', 1, [
    'include' => ['methods']
]);

var_dump($shipment->getMethod()->getId());

print('request count: '. $client->getRequestCount());

echo PHP_EOL;

die;
