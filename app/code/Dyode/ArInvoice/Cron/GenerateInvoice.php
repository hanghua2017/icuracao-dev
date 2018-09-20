<?php
namespace Magento\SampleMinimal\Cron;
use \Psr\Log\LoggerInterface;

class ApprovedFraudOrders
 {
    protected $logger;

    /**
     * @var \Dyode\ArInvoice\Model\ArInvoice $arInvoice
     **/
    protected $arInvoice;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * Constructor
     *
     * @param \Dyode\ArInvoice\Model\ArInvoice $arInvoice
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        \Dyode\ArInvoice\Model\ArInvoice $arInvoice,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->logger = $logger;
        $this->arInvoice = $arInvoice;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

/**
   * Write to system.log
   *
   * @return void
   */

    public function execute() {
        $this->logger->info('Cron Works');
        $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        $collection->addFieldToFilter('status', 'review_order');
        foreach ($collection as $salesOrder) {
            $this->arInvoice->createInvoice($salesOrder->getId());
            # Link AppleCareSetWarranty
        }
    }

}