<?php
namespace Dyode\Emarsys\Model;

use \Magento\Framework\Model\AbstractModel;

class Order extends \Magento\Framework\Model\AbstractModel {
   
	protected $orderRepository;

	protected $_productRepositoryFactory;

	protected $auditLog;

   	public function __construct( 
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		\Dyode\Emarsys\Helper\Data $emarsysHelper,
		\Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
	) {
		$this->orderRepository = $orderRepository;
		$this->emarsysSend = $emarsysHelper;
		$this->auditLog = $auditLog;
		$this->_productRepositoryFactory = $productRepositoryFactory;
	}

	public function sendConfirmationEmail($order_id){
		try {	  
    		$order = $this->orderRepository->get($order_id);
    		$customer_firstname = $order->getCustomerFirstname();
    		$customer_email = $order->getCustomerEmail();
    		$increment_id = $order->getIncrementId();
    		$estimate_number = $order->getEstimatenumber();
    		$order_date = date('l jS \of F Y', strtotime($order->getCreatedAt()));
			$shippingaddress = $_order->getShippingAddress()->getData();
			$billingaddress = $_order->getBillingAddress()->getData();
			$billing_street = $billingaddress['street'];
			$billing_city = $billingaddress['city'];
			$billing_region = $billingaddress['region'];
			$billing_postcode = $billingaddress['postcode'];

			$shipping_street = $shippingaddress['street'];
			$shipping_city = $shippingaddress['city'];
			$shipping_region = $shippingaddress['region'];
			$shipping_postcode = $shippingaddress['postcode'];

    		$subtotal = round($order->getSubtotal(), 2);
    		$shipping_price = round($order->getShippingAmount(), 2);
    		$discount = round($order->getDiscountAmount() , 2);
    		$tax = round($order->getTaxAmount(), 2);
    		$grand_total = round($order->getGrandTotal(), 2);
    	  
    		$items_data = '';
    		$ordered_items = $order->getAllItems();
    		foreach ($ordered_items as $item) {			
    			$product = $this->_productRepositoryFactory->create()->getById($item->getProductId());
    			$product_image = $product->getData('image')->resize(150);
    			$product_name = $product->getName();
    			$product_sku = $product->getSku();
    			$product_qty = round($item->getQtyOrdered(), 0);
    			$product_price = round($item->getPrice(), 2);
    			 
    			$items_data .= '
					{
					    "item_Image": "' . $product_image . '",
					    "product_Name": "' . $this->sanitize($product_name) . '",
					    "product_sku": "' . $this->sanitize($product_sku) . '",
					    "product_qty": "' . $product_qty . '",
					    "product_price": "$' . $product_price . '"
					},
				';
    		}
    		$items_data = trim(trim($items_data), ',');
    	  
    		$data = '
		     {
		     	"key_id": "3",
		     	"external_id": "' . $customer_email . '",
		     	"data": {
			        "order_details":
				    [{
				    	"order_customername": "' . $customer_firstname . '",
					    "order_incrementid": "' . $increment_id . '",
					    "order_createdat": "' . $order_date . '",
					    "billing_street": "' . $this->sanitize($billing_street) . '",
					    "billing_city": "' . $this->sanitize($billing_city) . '",
					    "billing_region": "' . $billing_region . '",
					    "billing_postcode": "' . $billing_postcode . '",
					    "shipping_street": "' . $this->sanitize($shipping_street) . '",
					    "shipping_city": "' . $this->sanitize($shipping_city) . '",
					    "shipping_region": "' . $shipping_region . '",
					    "shipping_postcode": "' . $shipping_postcode . '"
					}],
			        "order_items":
					[
					'; 
					$data .= $items_data;
					$store_name = 'icuracao.com';			
					$data .= '
		            ],
			    	"order_totals":
	    			[{
			            "subtotal": "' . $subtotal . '",
			            "tax": "' . $tax . '",
			            "shipping_price": "' . $shipping_price . '",
			            "discount": "' . $discount . '",
			            "grand_total": "' . $grand_total . '"
			        }],
					"order_notes":
			        [{
			            "customer_note": "",
			            "store_name": "' . $store_name . '"
			        }]
		     	},
		     	"contacts": null
		     } ';
    	  
    		$result = $this->emarsysSend->send('POST', 'event/1226/trigger', $data);
            $this->arErrorLogs('sendConfirmationEmail', 'Order confirmation mail dent to '.$customer_email);
    	} catch (Exception $ex){
			$error = $ex->getMessage();
			$this->arErrorLogs('Error - sendConfirmationEmail', $error);
    	}
    }
    
    public function customerRequestCancellationNotification ($increment_id, $customer_email, $customer_name) {
		try {
    		$data = '
		     {
		     	"key_id": "3",
		     	"external_id": "' . $customer_email . '",
		     	"data": {
			        "order_details":
				    [{
				    	"customer_name": "' . $customer_name . '",
					    "order_number": "' . $increment_id . '",
					    "item_name": "' . $item_info . '"
					}]
		     	},
		     	"contacts": null
		     } ';
    
    		$result = $this->emarsysSend->send('POST', 'event/2580/trigger', $data);
    		$result_json = json_decode($result);
    		$result_code = $result_json->replyCode;
    		$result_msg  = $result_json->replyText;
    		$notified = ($result_code == 0 && strtoupper($result_msg) == 'OK') ? 1 : 0;
    		if ($notified == 1) {
    			$message = 'Cancellation Email notification was sent successfully!';
    		} else {
    			$message = 'Cancellation Email notification failed due to ' .  $result_msg;
    		}
            $this->arErrorLogs('customerRequestCancellationNotification', 'Cancellation mail sent to '.$customer_email);
    	} catch (Exception $ex){
			$message = $ex->getMessage();
			$this->arErrorLogs('Error - customerRequestCancellationNotification', $message);
    	}
    	return $message;
    }

    public function outofstockCancellationNotification ($increment_id, $product_name, $customer_email, $customer_name) {
		try {

    		$data = '
		     {
		     	"key_id": "3",
		     	"external_id": "' . $customer_email . '",
		     	"data": {
			        "order_details":
				    [{
				    	"customer_name": "' . $customer_name . '",
					    "order_number": "' . $increment_id . '"
					}],
			        "order_items":
					[{
						    "product_name": "' . $product_name . '"
					}]
		     	},
		     	"contacts": null
		     } ';
    			
    		$result = $this->emarsysSend->send('POST', 'event/2556/trigger', $data);
    		$result_json = json_decode($result);
    		$result_code = $result_json->replyCode;
    		$result_msg  = $result_json->replyText;
    		$notified = ($result_code == 0 && strtoupper($result_msg) == 'OK') ? 1 : 0;
    		if ($notified == 1) {
    			$message = 'Out of stock Email notification was sent successfully!';
    		} else {
    			$message = 'Out of stock Email notification failed due to ' .  $result_msg;
    		}
            $this->arErrorLogs('outofstockCancellationNotification', 'Outofstock Cancellation notification sent to '.$customer_email);
    	} catch (Exception $ex){
			$message = $ex->getMessage();
			$this->arErrorLogs('Error - outofstockCancellationNotification', $message);
    	}
    	
    	return $message;
    }

    public function notifyStorePickup () {
        
    }

    public function sendOutofstockNotification ($order, $orderitem){
		try{
			$product = $this->_productRepositoryFactory->create()->getById($orderitem->getProductId());
			$email = $order->getCustomerEmail();
			$customer_firstname = $order->getCustomerFirstname();
			$customer_email = $order->getCustomerEmail();
			$increment_id = $order->getIncrementId();
			$estimate_number = $order->getEstimatenumber();
			$order_date = date('l jS \of F Y', strtotime($order->getCreatedAt()));
			$product_image = $product->getData('image')->resize(150);
			$product_name = $this->sanitize($product->getName());
			$product_sku = $this->sanitize($product->getSku());

			$data = '
			{
				"key_id": "3",
				"external_id": "' . $email . '",
				"data": {
					"global":
					{
						"order_CustomerFirstname": "' . $customer_firstname . '",
						"order_IncrementId": "' . $increment_id . '",
						"order_Estimatenumber": "' . $estimate_number . '",
						"order_CreatedAt": "' . $order_date . '",
						"customer_email": "' . $customer_email . '",
						"item_Image": "' . $product_image . '",
						"product_Name": "' . $product_name . '",
						"sku": "' . $product_sku . '"
					}
				},
				"contacts": null
			} ';
		
			$result = $this->emarsysSend->send('POST', 'event/2030/trigger', $data);
			$this->arErrorLogs('sendOutofstockNotification', 'Out of stock notification sent to '.$email);
		} catch (Exception $ex){
			$message = $ex->getMessage();
			$this->arErrorLogs('Error - sendOutofstockNotification', $message);
		}	 
	}

	public function arErrorLogs($action, $description)
    {
        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id'     => "",
            'action'      => $action,
            'description' => $description,
            'client_ip'   => "",
            'module_name' => "Dyode_Emarsys",
        ]);
    }
}
