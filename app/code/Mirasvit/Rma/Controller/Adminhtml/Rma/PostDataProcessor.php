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


namespace Mirasvit\Rma\Controller\Adminhtml\Rma;

use Mirasvit\Rma\Api\Data\OfflineItemInterface;

class PostDataProcessor
{
    public function __construct(
        \Mirasvit\Rma\Api\Repository\OfflineOrderRepositoryInterface $offlineOrderRepository,
        \Mirasvit\Rma\Service\Order\OrderAbstractFactory $orderAbstractFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
    ) {
        $this->offlineOrderRepository = $offlineOrderRepository;
        $this->orderAbstractFactory   = $orderAbstractFactory;
        $this->dateFilter             = $dateFilter;
        $this->messageManager         = $messageManager;
        $this->validatorFactory       = $validatorFactory;
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
     * Filtering posted data. Return only RMA data.
     *
     * @param array $data
     * @return array
     */
    public function filterRmaData($data)
    {
        $newData = $data;
        unset($newData['items']);

        if (empty($newData['return_address'])) {
            unset($newData['return_address']);
        }

        return $newData;
    }

    /**
     * Filtering posted data. Return only RMA items.
     *
     * @param array $data
     * @return array
     */
    public function filterRmaItems($data)
    {
        $items = $data['items'];
        foreach ($items as $k => $item) {
            if (!empty($item['is_offline'])) {
                $item[OfflineItemInterface::KEY_OFFLINE_ORDER_ID] = $data['order_id'];
            }
            if (!(int) $item['reason_id']) {
                unset($item['reason_id']);
            }
            if (!(int) $item['resolution_id']) {
                unset($item['resolution_id']);
            }
            if (!(int) $item['condition_id']) {
                unset($item['condition_id']);
            }
            $items[$k] = $item;
        }
        return $items;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    public function validate($data)
    {
        return $this->validateRequireEntry($data) && $this->validateItemsQty($data);
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
}
