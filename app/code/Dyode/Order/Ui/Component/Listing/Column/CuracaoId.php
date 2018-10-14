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
 * Datasource for curacaoid column
 * Class
 * @category Dyode
 * @package  Dyode_Order
 * @author   Nithin
 */
class CuracaoId extends Column
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
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
    SearchCriteriaBuilder $criteria,
    array $components = [],
    array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
              $order  = $this->_orderRepository->get($item["entity_id"]);
              $estimate = $order->getCuracaocustomernumber();
              $item[$this->getData('name')] = $estimate;
              //get customer object
              // if(!$order->getCustomerIsGuest()){
              //   $customer = $this->customerRepositoryInterface->getById($order->getCustomerId(), $websiteId = null);
              //   if($customer){
              //     $curacaoId = $customer->getCustomAttribute('curacaocustid');
              //     if($curacaoId!=NULL){
              //       $item[$this->getData('name')] = $customer->getCustomAttribute('curacaocustid');
              //     }
              //      $item[$this->getData('name')] = 'null';
              //   }
              // }
          }
      }
      return $dataSource;
    }
}
