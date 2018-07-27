<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Controller\Test;

class Index extends \Magento\Framework\App\Action\Action
{
    // protected $orderCollection;
    /**
     * @var \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     **/
    protected $_arInvoiceHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
        // \Dyode\ArInvoice\Model\OrderCollection $orderCollection
    ) { 
        // $this->orderCollection = $orderCollection;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        parent::__construct($context);
        
    }
    public function execute()
    {   
        //$orders = $this->orderCollection->getSalesOrderCollection();
        // echo $this->_arInvoiceHelper->prepareOrderItems(1);
         $this->_arInvoiceHelper->getSkuInventory("32A-061-101946", 2);
        // echo $this->_arInvoiceHelper->getProductType('45Q-RTY');
        // $this->_arInvoiceHelper->getDomesticInventory('32A-061-101946',2,3);
        // var_dump($this->_arInvoiceHelper->getPendingEstimate('Sample Product'));
        // $this->_arInvoiceHelper->assignInventoryLocation('32A-061-101946');
        // $this->_arInvoiceHelper->getInventoryLevel('32A-061-101946','06');
        // $this->orderCollection->createInvoice(5);
        // var_dump($orders);
        die();
    }
}