<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\ResourceModel;

use Aheadworks\StoreLocator\Model\LocationStore as ModelLocationStore;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * LocationStore.
 */
class LocationStore extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_storelocator_location_store', ModelLocationStore::ENTITY_ID);
    }
}
