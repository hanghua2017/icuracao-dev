<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LocationRegistry.
 */
class LocationRegistry
{
    const REGISTRY_SEPARATOR = ':';

    /**
     * @var LocationFactory
     */
    private $locationFactory;

    /**
     * @var array
     */
    private $locationRegistryById = [];

    /**
     * @param LocationFactory $locationFactory
     */
    public function __construct(
        LocationFactory $locationFactory
    ) {
        $this->locationFactory = $locationFactory;
    }

    /**
     * @param string $locationId
     * @return Location
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function retrieve($locationId)
    {
        if (isset($this->locationRegistryById[$locationId])) {
            return $this->locationRegistryById[$locationId];
        }

        /** @var \Aheadworks\StoreLocator\Model\Location $location */
        $location = $this->locationFactory->create()->load($locationId);

        if (!$location->getLocationId()) {
            throw NoSuchEntityException::singleField('locationId', $locationId);
        } else {
            $this->locationRegistryById[$locationId] = $location;
            return $location;
        }
    }

    /**
     * @param int $locationId
     * @return void
     */
    public function remove($locationId)
    {
        if (isset($this->locationRegistryById[$locationId])) {
            unset($this->locationRegistryById[$locationId]);
        }
    }

    /**
     * @param Location $location
     * @return $this
     */
    public function push(Location $location)
    {
        $this->locationRegistryById[$location->getId()] = $location;
        return $this;
    }
}
