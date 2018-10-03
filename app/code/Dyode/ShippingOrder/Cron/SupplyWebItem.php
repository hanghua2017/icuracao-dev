<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ShippingOrder\Cron;

class SupplyWebItem
{
    /**
     * @var \Dyode\ShippingOrder\Helper\Data
     **/
    protected $_shippingOrderHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */     
    protected $_orderRepository;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder $sortBuilder
     */
    protected $_sortBuilder;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected $_productRepository;

    /**
     * Constructor
     *
     * @param \Dyode\ShippingOrder\Helper\Data $shippingOrderHelper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Api\SortOrderBuilder $sortBuilder
     */
    public function __construct(
        \Dyode\ShippingOrder\Helper\Data $shippingOrderHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SortOrderBuilder $sortBuilder
    ) {
        $this->_shippingOrderHelper = $shippingOrderHelper;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sortBuilder = $sortBuilder;
        $this->_productRepository = $productRepository;
    }

    /**
    * Supply Web Item -> execute
    *
    * @return void
    */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/supplywebitem.log");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $searchCriteria = $this->_searchCriteriaBuilder
                ->addFilter('status','processing','eq')
                ->addSortOrder($this->_sortBuilder->setField('entity_id')
                ->setDescendingDirection()->create())
                ->setPageSize(100)->setCurrentPage(1)->create();

        $to = date("Y-m-d h:i:s"); // current date
        $from = strtotime('-2 day', strtotime($to));
        $from = date('Y-m-d h:i:s', $from); // 2 days before

        $ordersList = $this->_orderRepository->getList($searchCriteria);
        $ordersList->addFieldToFilter('created_at', array('from'=>$from, 'to'=>$to));

        foreach ($ordersList as $order) {
            $invoiceNumber = $order->getData('estimatenumber');
            if (empty($invoiceNumber)) {
                $logger->info("Order Id : " . $order->getIncrementId());
                $logger->info("Invoice Number Not found ");
                throw new Exception("Invoice Number Not found " . " Order Id: " . $order->getIncrementId(), 1);
            }
            // $invoiceNumber = "ZEP591R";
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
                        $logger->info("Order Id : " . $order->getIncrementId());
                        $logger->info("Order Item Id : " . $orderItem->getId());
                        $logger->info("API Response not Found.");
                        throw new Exception("API Response not Found", 1);
                    }
                    if ($response->OK != true) {
                        array_push($shippedArray, $orderItem->getId());
                    } else {
                        $logger->info("Order Id : " . $order->getIncrementId());
                        $logger->info("Order Item Id : " . $orderItem->getId());
                        $logger->info($response->INFO);
                        throw new Exception($response->INFO, 1);
                    }
                }
            }
            // Changing Order Status to Complete if all items are shipped
            if (count($shippedArray) == count($order->getAllItems())) {
                $order->setState('complete')->setStatus('complete');
                $order->save();
            }
        }
    }
}