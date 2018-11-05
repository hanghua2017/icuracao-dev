<?php

namespace Dyode\InventoryLocation\Model;

use Dyode\InventoryLocation\Model\ResourceModel\Location as InventoryLocationResourceModel;

class Location extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(InventoryLocationResourceModel::class);
    }
}
