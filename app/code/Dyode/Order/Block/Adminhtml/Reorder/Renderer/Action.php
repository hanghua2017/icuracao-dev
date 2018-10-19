<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dyode\Order\Block\Adminhtml\Reorder\Renderer;

/**
 * Adminhtml alert queue grid block action item renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Action extends \Magento\Sales\Block\Adminhtml\Reorder\Renderer\Action
{

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_actions = [];
        if ($this->_salesReorder->canReorder($row->getId()) && $row->getStatus() != 'initial') { 
            $reorderAction = [
                '@' => [
                    'href' => $this->getUrl('sales/order_create/reorder', ['order_id' => $row->getId()]),
                ],
                '#' => __('Reorder'),
            ];
            $this->addToActions($reorderAction);
        } else {
            $reorderAction = [
                '@' => [
                    'href' => "javascript: alert('This order has not yet completed!');",
                ],
                '#' => __('Incomplete Order'),
            ];
            $this->addToActions($reorderAction);
        }
        $this->_eventManager->dispatch(
            'adminhtml_customer_orders_add_action_renderer',
            ['renderer' => $this, 'row' => $row]
        );
        return $this->_actionsToHtml();
    }
}
