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



namespace Mirasvit\Rma\Model\UI\Rma\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ItemsColumn extends Column
{
    public function __construct(
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->rmaOrderService = $rmaOrderService;
        $this->rmaFactory = $rmaFactory;
        $this->rmaSearchManagement = $rmaSearchManagement;
        $this->escaper = $escaper;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param string $value
     * @return string
     */
    private function getLocalizedValue($value)
    {
        if ($serialized = @unserialize($value)) {
            return array_values($serialized)[0];
        }
        return $value;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                $rma = $this->rmaFactory->create();
                $rma->getResource()->load($rma, $item['rma_id']);
                $order = $this->rmaOrderService->getOrder($rma);
                if ($order->getIsOffline()) {
                    $items = $this->rmaSearchManagement->getRequestedOfflineItems($rma);
                } else {
                    $items = $this->rmaSearchManagement->getRequestedItems($rma);
                }
                $s = '';
                foreach ($items as $currentItem) {
                    if ($currentItem->getIsOffline()) {
                        $orderItem = $currentItem;
                    } else {
                        $orderItem = $this->orderItemRepository->get($currentItem->getOrderItemId());
                    }

                    $s .= '<b>' . $this->escaper->escapeHtml($orderItem->getName()) . '</b>';
                    $s .= ' / ';
                    $s .= $currentItem->getReasonName() ?
                        $this->getLocalizedValue($currentItem->getReasonName()) : '-';
                    $s .= ' /  ';
                    $s .= $currentItem->getConditionName() ?
                        $this->getLocalizedValue($currentItem->getConditionName()) : '-';
                    $s .= ' / ';
                    $s .= $currentItem->getResolutionName() ?
                        $this->getLocalizedValue($currentItem->getResolutionName()) : '-';
                    $s .= '<br>';
                }

                $item[$name] = $s;
            }
        }

        return $dataSource;
    }
}