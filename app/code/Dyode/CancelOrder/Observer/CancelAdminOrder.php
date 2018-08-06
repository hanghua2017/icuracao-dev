<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\CancelOrder\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Dyode\CancelOrder\Helper\Data;
use \Magento\Framework\Event\Manager;

class CancelAdminOrder implements ObserverInterface
{	
	// /**
	//  * @var \Magento\Framework\Event\Observer
	//  */
	// private $_eventManager;

	// /**
	//  * @var \Dyode\CancelOrder\Helper\Data
	//  */
	// protected $_cancelOrderHelper;
	// /**
	//  * Construct
	//  * 
	//  */
	// public function __construct(
	// 	\Magento\Framework\View\Element\Context $context,
	// 	\Magento\Framework\Event\Manager $eventManager,
	// 	\Dyode\CancelOrder\Helper\Data $cancelOrderHelper		
	// ) {
	// 	$this->_eventManager = $eventManager;
	// 	$this->_cancelOrderHelper = $cancelOrderHelper;
	// 	parent::__construct($context);
	// }
	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $_curl;
	protected $_redirect;
    protected $_url;
	public function __construct(
		// \Magento\Framework\View\Element\Context $context,
		\Magento\Framework\UrlInterface $url,
		\Magento\Framework\App\Response\Http $redirect,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Dyode\CancelOrder\Helper\Data $cancelOrderHelper
	) {
		$this->_curl = $curl;
		$this->_url = $url;
		$this->_redirect = $redirect;
		$this->_cancelOrderHelper = $cancelOrderHelper;
		// parent::__construct($context);
	}

	public function execute(Observer $observer)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$logger = $objectManager->get("Psr\Log\LoggerInterface");
		$logger->info("Hello Test");
	
		$order = $observer->getEvent()->getOrder();
		$orderId = $order->getIncrementId();
		$logger->info('Cancel Order Id: ' . $orderId);

		$invNo = "ZEP58P6";
		$logger->info("InvNo:".$invNo);
		$logger->info('RESP URL: ' . $resp);
		
		$result = $this->_cancelOrderHelper->cancelEstimate($invNo);
		
		// $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/CancelEstimate?Inv_No=$invNo";
		// $logger->info("REST API URL:" . $url);

		$url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/CancelEstimate?Inv_No=$invNo";
        /*
        * Init Curl
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /*
        * Set Content Header
        */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: TEST-WNNxLUjBxA78J7s',
            'Content-Type: application/json',
            )
        );
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
		$result = curl_exec($ch);
		$logger->info("hello",print_r($ch, true));
		// $json_result = json_decode($result);
		// $data = $json_result->INFO;
		// $logger->info("data:",$data);
		// $this->_curl->get($url);
		// $this->_curl->addHeader("Content-Type", "application/json");
		// $this->_curl->addHeader("X-Api-Key", "TEST-WNNxLUjBxA78J7s");
		// $this->_curl->curlOption(CURLOPT_RETURNTRANSFER, true);
		// $this->_curl->curlOption(CURLOPT_CUSTOMREQUEST, "DELETE");
		// //if the method is post
		// // $this->_curl->post($url, $params);
		// //response will contain the output in form of JSON string
		// $response = $this->_curl->getBody();
		// $logger->info('Array Log'.print_r(json_decode($response), true));
		// // $logger->info("OrderId:".$orderId);
        // $eventData = $observer->getEvent()->getData();
        // if($eventData) {
        //     $logger->info("test event");
		// }
		
		// $response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);
		// $data = $response->INFO;
		// $logger->info("data:",$data);
		// $logger->log(100,print_r($response,true));
		
		// $this->_logger->log(100,print_r($response,true));
	}
}