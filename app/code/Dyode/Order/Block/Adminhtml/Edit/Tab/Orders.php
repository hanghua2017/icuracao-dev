<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dyode\Order\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer orders grid block
 *
 * @api
 * @since 100.0.2
 */
class Orders extends \Magento\Customer\Block\Adminhtml\Edit\Tab\Orders
{

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        // Check if incomplete show popup
        if(strpos(strtolower($row->getStatus()), 'initial') !== false)
        {
            return "javascript: alert('This order has not yet completed!');";
        }

        return $this->getUrl('sales/order/view', ['order_id' => $row->getId()]);
    }
}
