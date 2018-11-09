<?php
namespace Dyode\ProcessEstimate\Model;

use Magento\Sales\Model\Order;

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
			$clientIP = "";
			///fetch orders with status 'process estimate'
		    $orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter(
		        'status',
		        ['in' => array('processestimate')]
		    );

		    foreach ($orders as $order) {
		    	$paymentMethod = $order->getPayment()->getMethod();
                if ((strpos($paymentMethod, 'authorizenet') !== false) || (strpos($paymentMethod, 'authnetcim') !== false)) {
		    		$Signify_Required = true;
		    		$orderTotal = $order->getGrandTotal(); //order total
		    		$payment = $order->getPayment();
		    		($payment->getAmountPaid()) ? $amountPaid =  $payment->getAmountPaid() : $amountPaid = $payment->getAmountAuthorized();
 					//total amount paid by the customer
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
		    	$orderState = Order::STATE_PROCESSING;
				$order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
				$order->save();		
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
		$customerID = (!empty($order->getData('curacaocustomernumber'))) ? $order->getData('curacaocustomernumber') : '500-8555';
		$invoiceNumber = (!empty($order->getData('estimatenumber'))) ? $order->getData('estimatenumber') : '';
		$payment = $order->getPayment();
	    ($payment->getAmountPaid()) ? $amountPaid =  $payment->getAmountPaid() : $amountPaid = $payment->getAmountAuthorized();
	    $referenceID = $order->getIncrementId();
	    //calls webDownPayment helper function for the API response
	    $this->helper->webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID);
	}

	//process Supply Invoice API
	public function setSupplyInvoice($order){
		$firstName = (!empty($order->getCustomerFirstname())) ? $order->getCustomerFirstname() : $order->getShippingAddress()->getFirstname();
		$lastName = (!empty($order->getCustomerLastname())) ? $order->getCustomerLastname() : $order->getShippingAddress()->getLastname();
		$email = $order->getCustomerEmail();

		$invoiceNumber = (!empty($order->getData('estimatenumber'))) ? $order->getData('estimatenumber') : '';

		/*
		$invoice_details = $order->getInvoiceCollection();
		foreach ($invoice_details as $invoice) {
		 $invoiceNumber = $invoice->getIncrementId();
		}
		*/

		//calls goSupplyInvoice helper function for the API response
		$this->helper->goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email);
	}
}
