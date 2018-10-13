<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\Order\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Datasource for estimatenumber column
 * Class
 * @category Dyode
 * @package  Dyode_Order
 * @author   Nithin
 */
class EstimateNumber extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;
    /**
     * Constructor
     *
     */
    public function __construct(ContextInterface $context,
      UiComponentFactory $uiComponentFactory,
      OrderRepositoryInterface $orderRepository,
      SearchCriteriaBuilder $criteria,
      array $components = [],
      array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->_orderRepository->get($item["entity_id"]);
                $estimate = $order->getEstimatenumber();
                $item[$this->getData('name')] = $estimate;
            }
        }
        return $dataSource;
    }
}
