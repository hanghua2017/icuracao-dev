<?php

namespace Dyode\InventoryLocation\Model\ResourceModel;

class Location extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('location_inventory', 'id');
    }
}
