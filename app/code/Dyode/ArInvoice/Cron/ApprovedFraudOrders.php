<?php
namespace Magento\SampleMinimal\Cron;
use \Psr\Log\LoggerInterface;

class ApprovedFraudOrders
 {
    protected $logger;

    /**
     * @var \Dyode\ArInvoice\Model\OrderCollection $orderCollection
     **/
    protected $orderCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    public function __construct(
        LoggerInterface $logger,
        \Dyode\ArInvoice\Model\OrderCollection $orderCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->logger = $logger;
        $this->orderCollection = $orderCollection;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

/**
   * Write to system.log
   *
   * @return void
   */

    public function execute() {
        $this->logger->info('Cron Works');
        // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        // $collection->addFieldToFilter('status', 'approved_fraud');
        // foreach ($collection as $salesOrder) {
        //     $this->orderCollection->createInvoice($salesOrder->getId());
        // }
    }

}