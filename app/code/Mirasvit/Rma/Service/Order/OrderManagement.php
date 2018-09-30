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


namespace Mirasvit\Rma\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface;
use Mirasvit\Rma\Api\Data\RmaInterface;

class OrderManagement implements \Mirasvit\Rma\Api\Service\Order\OrderManagementInterface
{
    public function __construct(
        \Mirasvit\Rma\Api\Config\OfflineOrderConfigInterface $offlineOrderConfig,
        \Mirasvit\Rma\Api\Config\RmaPolicyConfigInterface $policyConfig,
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Model\ResourceModel\OrderStatusHistory\CollectionFactory $historyCollectionFactory,
        \Mirasvit\Rma\Model\OfflineOrderFactory $offlineOrderFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->offlineOrderConfig       = $offlineOrderConfig;
        $this->policyConfig             = $policyConfig;
        $this->offlineOrderRepository   = $offlineOrderRepository;
        $this->rmaRepository            = $rmaRepository;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->offlineOrderFactory      = $offlineOrderFactory;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->customerRepository       = $customerRepository;
        $this->orderRepository          = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $items = $this->getOriginAllowedOrderList($customer);

        if ($this->offlineOrderConfig->isOfflineOrdersEnabled()) {
            $items[OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER] = $this->offlineOrderFactory->create()
                ->setId(OfflineOrderConfigInterface::OFFLINE_ORDER_PLACEHOLDER)
                ->setCustomerId($customer->getId())
                ->setCustomerName($customer->getName())
                ->setCustomerEmail($customer->getEmail());
        }

        return $items;
    }

    /**
     * @param \Magento\Customer\Model\Customer|false $customer
     * @return OrderInterface[]
     */
    public function getOriginAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', $allowedStatuses, 'in')
            ->addFilter('customer_id', (int)$customer->getId())
            ->addFilter('entity_id', $this->OrderDateSql()->getColumnValues('order_id'), 'in')
            ;

        return $this->orderRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @param \Magento\Customer\Model\Customer|false $customer
     * @return OrderInterface[]
     */
    public function getOfflineAllowedOrderList(\Magento\Customer\Model\Customer $customer)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', (int)$customer->getId())
            ;

        return $this->offlineOrderRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerForOfflineOrder($order)
    {
        return $this->customerRepository->getById((int)$order->getCustomerId());
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaAmount($order)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(RmaInterface::KEY_ORDER_ID, $order->getId())
        ;

        return count($this->rmaRepository->getList($searchCriteria->create())->getItems());
    }

    /**
     * {@inheritdoc}
     */
    public function isReturnAllowed($order)
    {
        if (is_object($order)) {
            $order = $order->getId();
        }
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', $allowedStatuses, 'in')
            ->addFilter('entity_id', (int)$order)
        ;

        return (bool)$this->orderRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * @return \Mirasvit\Rma\Model\ResourceModel\OrderStatusHistory\Collection
     */
    public function OrderDateSql()
    {
        $allowedStatuses = $this->policyConfig->getAllowRmaInOrderStatuses();
        $returnPeriod    = (int)$this->policyConfig->getReturnPeriod();
        /** @var \Mirasvit\Rma\Model\ResourceModel\OrderStatusHistory\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->removeAllFieldsFromSelect()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('status', ['in' => $allowedStatuses])
            ->addFieldToFilter(
                new \Zend_Db_Expr('ADDDATE(created_at, '.$returnPeriod.')'),
                ['gt' => new \Zend_Db_Expr('NOW()')]
            )
        ;

        return $collection;
    }
}