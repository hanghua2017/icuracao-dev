<?php
namespace Dyode\ProcessEstimate\Model;

use \Magento\Framework\Model\AbstractModel;

class Estimate extends \Magento\Framework\View\Element\Template {

   protected $_orderCollectionFactory;

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

	//fetch and process orders
	public function getOrders() {

	    ///fetch orders with status 'process estimate'
	    $orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter(
	        'status',
	        ['in' => array('process estimate')]
	    );

	    foreach ($orders as $order) {
	    	$paymentMethod = $this->getPaymentMethod($order);	    
	    	if (strpos($paymentMethod, 'Authorize.net') !== false) {
	    		$Signify_Required = true;
	    		$orderTotal = $order->getGrandTotal(); //order total
	    		$payment = $order->getPayment();
	    		$amountPaid = $payment->getAmountPaid(); //total amount paid by the customer
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

	//get the payment method details used to place the order
	public function getPaymentMethod($order){
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();
		return $methodTitle; //returns title of payement method used
	}

	//process Post Downpayment API
	public function postDownPayment($order){
		$customerID = $order->getCustomerID();
		$invoice_details = $order->getInvoiceCollection();
		foreach ($invoice_details as $invoice) {
		 $invoiceNumber = $invoice->getIncrementId();
		}
		$payment = $order->getPayment();
	    $amountPaid = $payment->getAmountPaid();
	    $referenceID = $order->getId();
	    //calls webDownPayment helper function for the API response
	    $this->helper->webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID);
	}

	//process Supply Invoice API
	public function setSupplyInvoice($order){
		$firstName = $order->getCustomerFirstname();
		$lastName = $order->getCustomerLastname();
		$email = $order->getCustomerEmail();
		$invoice_details = $order->getInvoiceCollection();
		foreach ($invoice_details as $invoice) {
		 $invoiceNumber = $invoice->getIncrementId();
		}		
		//calls goSupplyInvoice helper function for the API response
		$this->helper->goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email);
	}
}
