<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$location = $objectManager->create('Aheadworks\StoreLocator\Model\Location');
/** @var Aheadworks\StoreLocator\Model\Location $location */
$location->setTitle('Test Location 2')
    ->setDescription('Description')
    ->setStatus(1)
    ->setSortOrder(1)
    ->setCountryId('US')
    ->setRegionId('1')
    ->setCity('Chicago')
    ->setStreet('1200 Street Address')
    ->setZip('47676')
    ->setPhone('1-541-754-3010')
    ->setZoom(12)
    ->setLatitude(33.5218)
    ->setLongitude(55.8112)
    ->setImage('image2.png')
    ->setCustomMarker('marker2.png')
    ->setStores([1]);

$location->isObjectNew(true);
$location->save();
