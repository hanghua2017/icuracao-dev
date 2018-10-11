<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Model\ResourceModel;

/**
 * Class ProductConversion
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class ProductConversion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_conversion_product', 'id');
    }
}