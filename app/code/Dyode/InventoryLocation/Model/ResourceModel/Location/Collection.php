<?php

namespace Dyode\InventoryLocation\Model\ResourceModel\Location;

use Dyode\InventoryLocation\Model\Location as InventoryLocationModel;
use Dyode\InventoryLocation\Model\ResourceModel\Location as InventoryLocationResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(InventoryLocationModel::class, InventoryLocationResourceModel::class);
    }

}
