<?php
namespace Dyode\InventoryLocation\Model;

class Location extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dyode\InventoryLocation\Model\ResourceModel\Location');
    }
}
?>