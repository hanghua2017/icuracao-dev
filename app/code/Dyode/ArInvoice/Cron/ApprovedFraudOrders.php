<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ArInvoice\Cron;

class ApprovedFraudOrders
{
    /**
     * @var \Dyode\ArInvoice\Model\ArInvoice $_arInvoiceModel
     **/
    protected $_arInvoiceModel;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Dyode\ArInvoice\Helper\Data $_arInvoiceHelper 
     **/
    protected $_arInvoiceHelper;

    /**
     * Constructor
     *
     * @param \Dyode\ArInvoice\Model\ArInvoice $arInvoiceModel
     * @param \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        \Dyode\ArInvoice\Model\ArInvoice $arInvoiceModel,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->_arInvoiceModel = $arInvoiceModel;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    /**
    * Approved Fraud Orders
    *
    * @return void
    */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/approvedfraudorderscron.log");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Cron Works");

        $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        $collection->addFieldToFilter('status', 'approved_fraud');

        foreach ($collection as $salesOrder) {
            $this->_arInvoice->createInvoice($salesOrder->getId());
            $this->_arInvoiceHelper->linkAppleCare($salesOrder);
        }
    }

}