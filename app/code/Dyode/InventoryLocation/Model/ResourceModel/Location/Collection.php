<?php

namespace Dyode\InventoryLocation\Model\ResourceModel\Location;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dyode\InventoryLocation\Model\Location', 'Dyode\InventoryLocation\Model\ResourceModel\Location');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>