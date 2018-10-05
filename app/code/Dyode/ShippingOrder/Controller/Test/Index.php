<?php

namespace Dyode\ShippingOrder\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_invoiceModel;
    protected $_shipmentModel;

    /**
     * @var \Dyode\ShippingOrder\Helper\Data
     **/
    protected $_shippingOrderHelper;

    protected $_orderRepository;
    
    protected $_sortBuilder;

    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ShippingOrder\Model\Order\Invoice $invoiceModel,
        \Dyode\ShippingOrder\Model\Order\Shipment $shipmentModel,
        \Dyode\ShippingOrder\Helper\Data $shippingOrderHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SortOrderBuilder $sortBuilder
    ) {
        $this->_invoiceModel = $invoiceModel;
        $this->_shipmentModel = $shipmentModel;
        $this->_shippingOrderHelper = $shippingOrderHelper;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sortBuilder = $sortBuilder;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $searchCriteria = $this->_searchCriteriaBuilder
                // ->addFilter('status','pending','eq')
                ->addSortOrder($this->_sortBuilder->setField('entity_id')
                ->setDescendingDirection()->create())
                ->setPageSize(100)->setCurrentPage(1)->create();

    $to = date("Y-m-d h:i:s"); // current date
    $from = strtotime('-2 day', strtotime($to));
    $from = date('Y-m-d h:i:s', $from); // 2 days before

    $ordersList = $this->_orderRepository->getList($searchCriteria);
    $ordersList->addFieldToFilter('created_at', array('from'=>$from, 'to'=>$to));

    //     // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
    //     // $collection->addFieldToFilter('status', 'processing');
    //     foreach ($ordersList as $salesOrder) {
    //         echo "Hello";
    //         echo $salesOrder->getIncrementId();
    //         print_r($salesOrder->getData());
    //         if ($salesOrder->hasShipments() or $salesOrder->hasInvoices()) {
    //             echo "World";
    //             print_r($salesOrder->hasShipments());
    //             echo "<br>";
    //             $salesOrder->getIncrementId();
    //             // break;
    //         }

    //         echo "<br>";
    //         // $this->arInvoice->createInvoice($salesOrder->getId());
    //         # Link AppleCareSetWarranty
    //     }
        $order = $this->_orderRepository->get(13045);
        echo "<pre>";   
        $invoiceNumber = $order->getData('estimatenumber');
        if (empty($invoiceNumber)) {
            throw new Exception("Invoice Number Not found " . " Order Id: " . $order->getIncrementId(), 1);
        }
        $invoiceNumber = "ZEP591R";
        $shippedArray = array();
        foreach ($order->getAllItems() as $orderItem) {
            $itemStatus = $orderItem->getStatus();
            if ($itemStatus == 'Shipped') {
                $productId = $orderItem->getProductId();
                $product = $this->_productRepository->getById($productId);
                $itemSku = $product->getSku();
                $itemName = $product->getName();
                $qty = (int)$orderItem->getQtyOrdered();
                // Calling the API
                $response = $this->_shippingOrderHelper->supplyWebItem($invoiceNumber, $itemSku, $itemName, $qty);
                
                if (empty($response)) {
                    throw new Exception("API Response not Found", 1);
                }
                if ($response->OK != true) {
                    array_push($shippedArray, $orderItem->getId());
                } else {
                    throw new Exception($response->INFO, 1);
                }
            }
        }
        if (count($shippedArray) == count($order->getAllItems())) {
            $order->setState('complete')->setStatus('complete');
            $order->save();
        }
        // $this->
        // echo "Hello";
        die();
        // die();
        #dummy values
        // $invoiceNumber = "ZEP591R";
        // $itemId =  "09A-RA3-RS16FT5050RB";
        // $itemName = "RACE SPORT 16FT RGB ST";
        // $qty = 1;
        // $isSet = false;
        // $respose = $this->_shippingOrderHelper->supplyWebItem($invoiceNumber, $itemId, $itemName, $qty, $isSet);
        // print_r($respose->INFO);
        // die();
        $orderId = 5;   //order id for which want to create invoice
        // $invoiceResponse = $this->_invoiceModel->createInvoice($orderId);
        $shipmentResponse = $this->_shipmentModel->createShipment($orderId);
        die();
    }
}