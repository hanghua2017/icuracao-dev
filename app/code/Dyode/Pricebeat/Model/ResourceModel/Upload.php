<?php
namespace Dyode\Pricebeat\Model\ResourceModel;

class Upload extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dyode_pricebeat_form', 'form_id');
    }
}
?>
