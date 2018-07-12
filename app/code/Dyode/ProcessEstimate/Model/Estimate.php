<?php
namespace Dyode\ProcessEstimate\Model;

use \Magento\Framework\Model\AbstractModel;

class Estimate extends \Magento\Framework\View\Element\Template {
    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
   protected $_orderCollectionFactory;
   /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
   protected $orders;
   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory, 
	\Dyode\ProcessEstimate\Helper\Data $helper,
	array $data = []
	) {
	    $this->_orderCollectionFactory = $orderCollectionFactory;
	    $this->helper = $helper;
	    parent::__construct($context, $data);
	}

	public function getOrders() {

	    $orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter(
	        'status',
	        ['in' => array('processing')]
	    );

	    foreach ($orders as $order) {

	    	$paymentMethod = $this->getPaymentMethod($order);	    
	    	if (strpos($paymentMethod, 'Authorize.net') !== false) {
	    		$Signify_Required = true;
	    		$orderTotal = $order->getGrandTotal();
	    		$payment = $order->getPayment();
	    		$amountPaid = $payment->getAmountPaid();
	    		if ($amountPaid >= $orderTotal) {
	    			$Cash_Amount = $amountPaid;
	    		} else {
	    			$Cash_Amount = $amountPaid;
	    		}
	    		if ($Cash_Amount > 0) {
	    			$this->postDownPayment($order);
	    		} else {
	    			$this->setSupplyInvoice($order);
	    		}
	    			
	    	} else {
	    		$this->setSupplyInvoice($order);
	    	}			
		}    
	}

	public function getPaymentMethod($order){
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();
		return $methodTitle;
	}

	public function postDownPayment($order){
		$customerID = $order->getCustomerID();
		$invoice_details = $order->getInvoiceCollection();
		foreach ($invoice_details as $invoice) {
		 $invoiceNumber = $invoice->getIncrementId();
		}
		$payment = $order->getPayment();
	    $amountPaid = $payment->getAmountPaid();
	    $this->helper->webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID);
	}

	public function setSupplyInvoice($order){
		$firstName = $order->getCustomerFirstname();
		$lastName = $order->getCustomerLastname();
		$email = $order->getCustomerEmail();
		$invoice_details = $order->getInvoiceCollection();
		foreach ($invoice_details as $invoice) {
		 $invoiceNumber = $invoice->getIncrementId();
		}		
		$this->helper->goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email);
	}
}
