<?php
namespace Dyode\Pricebeat\Model;

class Upload extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dyode\Pricebeat\Model\ResourceModel\Upload');
    }
}
?>
