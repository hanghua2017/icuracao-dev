<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ArInvoice\Cron;

class GenerateInvoice
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
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog

    ) {
        $this->_arInvoiceModel = $arInvoiceModel;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->auditLog = $auditLog;
    }

    /**
     * Generate Invoice
     *
     * @return void
     */
    public function execute()
    {
        $cronStatus = false;
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/generateinvoicecron.log");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Cron Works");

        $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        $collection->addFieldToFilter('status', 'pending');
        foreach ($collection as $salesOrder) {
            $cronStatus = $this->_arInvoiceModel->createInvoice($salesOrder->getId());
            $cronStatus = $this->_arInvoiceHelper->linkAppleCare($salesOrder);
        }

        if ($cronStatus) {
            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id' => "",
                'action' => 'AR Create Invoice Cron',
                'description' => "Success",
                'client_ip' => "",
                'module_name' => "Dyode_ArInvoice"
            ]);
        } else {
            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id' => "",
                'action' => 'AR Create Invoice Cron',
                'description' => "Failed",
                'client_ip' => "",
                'module_name' => "Dyode_ArInvoice"
            ]);
        }
    }
}