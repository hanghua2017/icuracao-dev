<?php
namespace Dyode\ProcessEstimate\Model;

class Estimate extends \Magento\Framework\Model\AbstractModel
{

   protected $_orderCollectionFactory;

   protected $orders;

   public function __construct( 
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory, 
	\Dyode\ProcessEstimate\Helper\Data $helper,
	\Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
	) {
	    $this->_orderCollectionFactory = $orderCollectionFactory;
	    $this->helper = $helper;
	    $this->auditLog = $auditLog;
	}

	//fetch and process orders
	public function getOrders() {
		try {
			$clientIP = $_SERVER['REMOTE_ADDR'];
			///fetch orders with status 'process estimate'
		    $orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter(
		        'status',
		        ['in' => array('processestimate')]
		    );

		    foreach ($orders as $order) {
		    	$paymentMethod = $order->getPayment()->getMethod();
		    	if (strpos($paymentMethod, 'authorizenet') !== false) {
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
	        $this->auditLog->saveAuditLog([
	            'user_id' => 'admin',
	            'action' => 'process estimate cron',
	            'description' => 'Process estimate cron successfully executed. ',
	            'client_ip' => $clientIP,
	            'module_name' => 'dyode_processestimate'
	        ]);
	    } catch (\Exception $exception) {
	        $this->auditLog->saveAuditLog([
	            'user_id' => 'admin',
	            'action' => 'process estimate cron',
	            'description' => $exception->getMessage(),
	            'client_ip' => $clientIP,
	            'module_name' => 'dyode_processestimate'
	        ]);
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
	    $referenceID = $order->getIncrementId();
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
