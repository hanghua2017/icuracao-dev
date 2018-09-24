<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

use Aheadworks\StoreLocator\Model\ResourceModel\LocationStore as LocationStoreResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class LocationStore.
 */
class LocationStore extends AbstractModel
{
    const ENTITY_ID = 'entity_id';
    const LOCATION_ID = 'location_id';
    const STORE_ID = 'store_id';

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(LocationStoreResource::class);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get location id.
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->_getData(self::LOCATION_ID);
    }

    /**
     * Set location id.
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * Get store id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * Set store id.
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
