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

class OrderColumn extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $helper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->rmaOrderService = $rmaOrderService;
        $this->rmaFactory = $rmaFactory;
        $this->escaper = $escaper;
        $this->helper = $helper;
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

                if ($name == 'exchange_order_ids' || $item[$name]) {
                    $rma = $this->rmaFactory->create();
                    $rma->getResource()->load($rma, $item[$rma->getIdFieldName()]);
                    $rma->getResource()->afterLoad($rma);
                    $str = '';
                    if ($rma->getData($name)) {
                        $order = $this->rmaOrderService->getOrder($rma);
                        if ($order->getIsOffline()) {
                            $str .= $this->escaper->escapeHtml($order->getReceiptNumber());
                        } else {
                            $str .= $order->getIncrementId();
                        }
                    }
                    $item[$name] = $str;
                } else {
                    $item[$name] = '';
                }

            }
        }

        return $dataSource;
    }
}