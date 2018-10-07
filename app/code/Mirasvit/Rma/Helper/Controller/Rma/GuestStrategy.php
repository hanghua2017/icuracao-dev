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



namespace Mirasvit\Rma\Helper\Controller\Rma;

use Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface;

class GuestStrategy extends AbstractStrategy
{

    public function __construct(
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Config\FrontendConfigInterface $frontendConfig,
        \Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface $offlineConfig,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Api\Service\Strategy\SearchInterface $strategySearch,
        \Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface $performerFactory,
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrl,
        \Mirasvit\Rma\Model\OfflineOrderFactory $offlineOrderFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->rmaUrl                 = $rmaUrl;
        $this->offlineOrderRepository = $offlineOrderRepository;
        $this->rmaRepository          = $rmaRepository;
        $this->frontendConfig         = $frontendConfig;
        $this->offlineConfig          = $offlineConfig;
        $this->rmaOrderService        = $rmaOrderService;
        $this->strategySearch         = $strategySearch;
        $this->offlineOrderFactory    = $offlineOrderFactory;
        $this->orderRepository        = $orderRepository;
        $this->customerSession        = $customerSession;
        $this->performerFactory       = $performerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequireCustomerAutorization()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaId(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $rma->getGuestId();
    }

    /**
     * {@inheritdoc}
     */
    public function initRma(\Magento\Framework\App\RequestInterface $request)
    {
        $id = $request->getParam('id');
        $rma = $this->rmaRepository->getByGuestId($id);

        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaList($order = null)
    {
        if ($this->customerSession->getRMAGuestOrderId() == OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER) {
            return [];
        }
        if ($this->frontendConfig->showGuestRmaByOrder()) {
            $customerId = 0;
            if (!$order) {
                $order = $this->getOrder();
            }
        } else {
            $customerId = $this->getOrder()->getCustomerId();
            if (!$customerId && $this->offlineConfig->isOfflineOrdersEnabled()) {
                if (!$order) {
                    $order = $this->getOrder();
                }
            }
        }

        return $this->strategySearch->getRmaList(
            $customerId,
            $order
        );
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        $orderId = $this->customerSession->getRMAGuestOrderId();
        $isOfflineOrder = $this->customerSession->getRMAGuestOrderIsOffline();
        if ($orderId == OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER) {
            $order = $this->offlineOrderFactory->create();
            if (!empty($this->customerSession->getRMAFirstname())) {
                $order->setCustomerFirstname($this->customerSession->getRMAFirstname());
            }
            if (!empty($this->customerSession->getRMALastname())) {
                $order->setCustomerLastname($this->customerSession->getRMALastname());
            }
            if (!empty($this->customerSession->getRMAEmail())) {
                $order->setCustomerEmail($this->customerSession->getRMAEmail());
            }
        } else {
            if ($isOfflineOrder) {
                $order = $this->offlineOrderRepository->get($orderId);
            } else {
                $order = $this->orderRepository->get($orderId);
            }
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerformer()
    {
        $order = $this->getOrder();
        $name = implode(
            ' ',
            [$order->getCustomerFirstname(), $order->getCustomerMiddlename(), $order->getCustomerLastname()]
        );

        return $this->performerFactory->create(
            \Mirasvit\Rma\Api\Service\Performer\PerformerFactoryInterface::GUEST,
            new \Magento\Framework\DataObject(
                [
                    'name'  => $name,
                    'email' => $order->getCustomerEmail(),
                    'id'    => $order->getCustomerId(),
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOrderList()
    {
        if ($this->getOrder()->getIsOffline()) {
            $name = $this->customerSession->getRMAFirstname() . ' ' . $this->customerSession->getRMALastname();
            return [
                OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER => $this->offlineOrderFactory->create()
                    ->setId(OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER)
                    ->setCustomerName($name)
                    ->setCustomerEmail($this->customerSession->getRMAEmail())
            ];
        }

        return [$this->getOrder()->getId() => $this->getOrder()];
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaUrl(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->rmaUrl->getGuestUrl($rma);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRmaUrl()
    {
        $order = $this->getOrder();
        if ($order->getIsOffline()) {
            return '';
        }
        return $this->rmaUrl->getCreateUrl($order);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrintUrl(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        return $this->rmaUrl->getGuestPrintUrl($rma);
    }
}