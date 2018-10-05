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


namespace Mirasvit\Rma\Controller\Rma;

use Mirasvit\Rma\Api\Data\OfflineItemInterface;

class PostDataProcessor
{
    public function __construct(
        \Mirasvit\Rma\Service\Item\ItemListBuilder $itemListBuilder,
        \Mirasvit\Rma\Api\Config\RmaRequirementConfigInterface $reasonsConfig,
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Mirasvit\Rma\Api\Service\Item\ItemManagement\QuantityInterface $itemQuantityManagement,
        \Mirasvit\Rma\Service\Order\OrderAbstractFactory $orderAbstractFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
    ) {
        $this->itemListBuilder        = $itemListBuilder;
        $this->reasonsConfig          = $reasonsConfig;
        $this->itemQuantityManagement = $itemQuantityManagement;
        $this->productRepository      = $productRepository;
        $this->dateFilter             = $dateFilter;
        $this->messageManager         = $messageManager;
        $this->validatorFactory       = $validatorFactory;
        $this->orderAbstractFactory   = $orderAbstractFactory;
        $this->offlineOrderRepository = $offlineOrderRepository;
    }

    /**
     * Filtering posted data. Return only RMA data.
     *
     * @param array $data
     * @return array
     */
    public function filterRmaData($data)
    {
        $newData = $data;
        unset($newData['items']);

        return $newData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function createOfflineOrder($data)
    {
        $order = $this->orderAbstractFactory->get($data);
        if ($order->getIsOffline()) {
            $order->setData($data);
            $this->offlineOrderRepository->save($order);
            $data['order_id'] = $order->getId();
        }

        return $data;
    }

    /**
     * Filtering posted data. Return only RMA items.
     *
     * @param array $data
     * @return array
     */
    public function filterRmaItems($data)
    {
        $order = $this->orderAbstractFactory->get($data);
        $itemCollection = $this->itemListBuilder->getList($order);

        $items = $data['items'];
        foreach ($items as $k => $item) {
            $item = $this->filterConditions($item);
            $item['order_id'] = $data['order_id'];
            $item['order_item_id'] = $k;

            if (!empty($item['is_offline'])) {
                $item[OfflineItemInterface::KEY_OFFLINE_ORDER_ID] = $data['order_id'];
            } else {
                $orderItem = $itemCollection[$k];
                if ($orderItem) {
                    $item['product_sku'] = $this->getProductSku($orderItem);
                }
            }

            $items[$k] = $item;
        }

        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return string
     */
    private function getProductSku($orderItem)
    {
        $options = $orderItem->getProductOptions();
        if (!empty($options['simple_sku'])) {
            $productSku = $this->productRepository->get($options['simple_sku'])->getsku();
        } else {
            $productSku = $orderItem->getProductSku() ?: $orderItem->getSku();
        }

        return $productSku;
    }

    /**
     * @param array $item
     * @return array
     */
    private function filterConditions($item)
    {
        if (isset($item['reason_id']) && !(int)$item['reason_id']) {
            unset($item['reason_id']);
        }
        if (isset($item['resolution_id']) && !(int)$item['resolution_id']) {
            unset($item['resolution_id']);
        }
        if (isset($item['condition_id']) && !(int)$item['condition_id']) {
            unset($item['condition_id']);
        }

        return $item;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool Return FALSE if someone item is invalid
     */
    public function validate($data)
    {
        return $this->validateRequireEntry($data) && $this->validateItemsQty($data) &&
            $this->isAvailableItemsQty($data) && $this->validateReasons($data);
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateReasons(array $data)
    {
        $items   = $data['items'];
        $reasons = explode(',', $this->reasonsConfig->getGeneralCustomerRequirement());

        foreach ($items as $item) {
            if (!$item['qty_requested']) {
                continue;
            }
            foreach ($reasons as $reason) {
                if ($item[$reason.'_id'] < 1) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'items' => __('Items'),
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addError(
                    __('To apply changes you should fill in required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }

    /**
     * Check if any item has qty > 0
     *
     * @param array $data
     * @return bool
     */
    public function validateItemsQty(array $data)
    {
        $isEmpty = true;
        foreach ($data['items'] as $item) {
            if ((int)$item['qty_requested'] > 0) {
                $isEmpty = false;
                break;
            }
        }
        if ($isEmpty) {
            $this->messageManager->addError(
                __("Please, add order items to the RMA (set 'Qty to Return')")
            );
            return false;
        }
        return true;
    }

    /**
     * Check if requested items is available for RMA
     *
     * @param array $data
     * @return bool
     */
    public function isAvailableItemsQty(array $data)
    {
        $order = $this->orderAbstractFactory->get($data);
        $items = $this->itemListBuilder->getList($order);
        $isAvailable = true;
        foreach ($items as $orderItem) {
            foreach ($data['items'] as $k => $item) {
                if ($orderItem->getOrderItemId() != $k || (int)$item['qty_requested'] <= 0) {
                    continue;
                }
                if (!$this->itemQuantityManagement->getQtyAvailable($orderItem)) {
                    $this->messageManager->addErrorMessage(
                        __("Please, set correct order items Quantity to return")
                    );
                    $isAvailable = false;
                    break;
                }
            }
        }

        return $isAvailable;
    }
}
