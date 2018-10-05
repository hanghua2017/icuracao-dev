<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Block\Rma\NewRma\Step2;

class Order extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->strategy = $strategyFactory->create();
        $this->customerSession = $customerSession;

        $this->storeId  = $context->getStoreManager()->getStore()->getId();

        parent::__construct($context, $data);
    }

    /**
     * @var \Mirasvit\Rma\Model\OfflineOrder
     */
    protected $order;

    /**
     * @return \Mirasvit\Rma\Model\OfflineOrder
     */
    public function getOrder()
    {
        if (!$this->order) {
            if ($orderId = $this->getRequest()->getParam('order_id')) {
                $items = $this->strategy->getAllowedOrderList();
                if (isset($items[$orderId])) {
                    $this->order = $items[$orderId];
                }
            }
        }
        return $this->order;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}