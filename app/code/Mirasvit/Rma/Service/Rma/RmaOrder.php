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


namespace Mirasvit\Rma\Service\Rma;

use Mirasvit\Rma\Api\Data\RmaInterface;

class RmaOrder implements \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Rma\Api\Config\RmaConfigInterface $rmaConfig,
        \Mirasvit\Rma\Api\Repository\ItemRepositoryInterface $itemRepository,
        \Mirasvit\Rma\Api\Repository\OfflineItemRepositoryInterface $offlineItemRepository,
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->rmaConfig              = $rmaConfig;
        $this->itemRepository         = $itemRepository;
        $this->offlineItemRepository  = $offlineItemRepository;
        $this->offlineOrderRepository = $offlineOrderRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->orderItemRepository    = $orderItemRepository;
        $this->orderRepository        = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(RmaInterface $rma)
    {
        $results = $this->getOfflineRmaItemCollection($rma);
        if ($results->getTotalCount()) {
            return $this->offlineOrderRepository->get($rma->getOrderId());
        }
        $results = $this->getRmaItemCollection($rma);
        if ($results->getTotalCount()) {
            return $this->orderRepository->get($rma->getOrderId());
        }
    }

    /**
     * @param RmaInterface $rma
     * @return \Magento\Framework\Api\SearchResults
     */
    private function getOfflineRmaItemCollection(RmaInterface $rma)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId())
        ;
        return $this->offlineItemRepository->getList($searchCriteria->create());
    }

    /**
     * @param RmaInterface $rma
     * @return \Magento\Framework\Api\SearchResults
     */
    private function getRmaItemCollection(RmaInterface $rma)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId())
        ;
        $items = $this->itemRepository->getList($searchCriteria->create());
        if (!$items->getTotalCount() && $rma->getOrderId()) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('order_id', $rma->getOrderId())
            ;
            $items = $this->orderItemRepository->getList($searchCriteria->create());
        }
        return $items;
    }
}

