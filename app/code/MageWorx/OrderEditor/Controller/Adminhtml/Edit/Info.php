<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use Magento\Sales\Api\OrderRepositoryInterface;
use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Info extends AbstractAction
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param Quote $quote
     * @param Order $order
     * @param ShippingModel $shipping
     * @param OrderRepositoryInterface $orderRepository
     * @internal param \MageWorx\OrderEditor\Model\Customer $customer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Quote $quote,
        Order $order,
        ShippingModel $shipping,
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultFactory,
            $helper,
            $scopeConfig,
            $quote,
            $order,
            $shipping
        );
        $this->localeDate = $timezone;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \Exception
     */
    protected function update()
    {
        $order = $this->loadOrder();
        $params = $this->getRequest()->getParams();
        $infoData = !empty($params['order']['info']) ? $params['order']['info'] : [];
        if (isset($infoData['created_at'])) {
            $createdAt = new \DateTime($infoData['created_at'], new \DateTimeZone($this->localeDate->getConfigTimezone()));
            $createdAt->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $infoData['created_at'] = $createdAt;
        }
        $order->addData($infoData);
        try {
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }
}
