<?php
namespace Dyode\Test\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		\Dyode\Emarsys\Model\Order $emarsysOrder,
		\Dyode\SetInventory\Model\Update $update)
	{
		$this->update = $update;
		$this->_pageFactory = $pageFactory;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->productRepository = $productRepository;
		$this->emarsys = $emarsysOrder;
		$this->orderRepository = $orderRepository;
		$this->_productRepositoryFactory = $productRepositoryFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if($id == '1'){
			$order = $this->orderRepository->get('1000071667');
			$this->emarsys->sendConfirmationEmail('1000071667');
			$this->emarsys->customerRequestCancellationNotification ('1000071667', 'nithinl4life@gmail.com', 'Nithin');
			$this->emarsys->outofstockCancellationNotification ('1000071667', 'Test', 'nithinl4life@gmail.com', 'Nithin');
			$ordered_items = $order->getAllItems();
    		foreach ($ordered_items as $item) {
				$this->emarsys->sendOutofstockNotification ($order, $item);
			}
		
		}
        $product = $this->_productRepositoryFactory->create()->getById('127043');
		if($id == '2'){
			$product->setStoreId(0);
			$product->setStatus(0);
			$product->setVisibiity(1);
			$product->save();
		}

		if($id == '3'){
			$product->setStatus(1);
			$product->setVisibiity(1);
			$product->save();
		}
		
		$productCollection = $this->productCollectionFactory->create();
        /** Apply filters here */
        $productCollection->addAttributeToSelect('*');
        $count = 0;
        foreach ($productCollection as $product) {
			$product->setStoreId(0);
			$product->setSpecialPrice('');
			$product->setSpecialFromDate('');
			$product->setSpecialToDate('');
			$product->save();
         $count++;   
		}
		echo $count;
		
		echo "all special price dates updated";exit;
		$customer_firstname = 'Nithin';
		$increment_id = '1234567890';
		$estimate_number = 'ZSWERDF';
		$created_at = '24-10-2018';
		$customer_email = 'ntn.nithi@gmail.com';
		$customer_lastname = 'mohan';
		$customer_street = 'common street';
		$customer_city = 'alaska';
		$customer_region = 'pacific';
		$customer_postcode = '40085';
		$payMethod = 'curacap pay';
		$product_image = 'http://d312mf19e7cgro.cloudfront.net/catalog/product/cache/c687aa7517cf01e65c009f6943c2b1e9/2/5/25L-863-SM-T280NZKAX.jpg';
		$product_name = 'test product';
		$sku = 'AQW-FTY-345678';
		$qty[0] = 1;
		$qty[1] = 2;
		$store_name = 'curacao';
		$store_address = 'LA';
		$store_city = 'alaska';
		$store_state = 'CA';
		$store_zip = '400895';



		$data = '
		{
			"key_id": "3",
			"external_id": "' . $customer_email . '",
			"data": {
				"global":
				{
				"order_CustomerFirstname": "' . $customer_firstname . '",
				"order_IncrementId": "' . $increment_id . '",
				"order_Estimatenumber": "' . $estimate_number . '",
				"order_CreatedAt": "' . $created_at . '",
				"customer_email": "' . $customer_email . '",
				"order_CustomerLastname": "' . $customer_lastname . '",
				"street": "' . $customer_street . '",
				"billingAddress_City": "' . $customer_city . '",
				"billingAddress_Region": "' . $customer_region . '",
				"billingAddress_Postcode": "' . $customer_postcode . '",
				"payMethod": "' . $payMethod . '",
				"item_Image": "' . $product_image .'",
				"product_Name": "' . $product_name . '",
				"sku": "' . $sku . '",
				"qty1": "' . $qty[0] .'",
				"qty2": "' . $qty[1] .'",
				"store_name": "' . $store_name . '",
				"store_address": "' . $store_address . '",
				"store_city": "' . $store_city .'",
				"store_state": "' . $store_state . '",
				"store_zip": "' . $store_zip . '"
				}
			},
			"contacts": null
		} ';
    	 
    	$result = $this->send('POST', 'event/1457/trigger', $data);
	}

	public function send($requestType, $endPoint, $requestBody = '')
	{
		$this->_username = 'curacao005';
		$this->_secret = 'PfkKEedIbaON8GdzC0UW';
		$this->_suiteApiUrl = 'https://api.emarsys.net/api/v2/';

		if (!in_array($requestType, array('GET', 'POST', 'PUT', 'DELETE'))) {
			throw new Exception('Send first parameter must be "GET", "POST", "PUT" or "DELETE"');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		switch ($requestType)
		{
			case 'GET':
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
				break;
		}
		curl_setopt($ch, CURLOPT_HEADER, true);

		$requestUri = $this->_suiteApiUrl . $endPoint;
		curl_setopt($ch, CURLOPT_URL, $requestUri);
		
		/**
		 * We add X-WSSE header for authentication.
		 * Always use random 'nonce' for increased security.
		 * timestamp: the current date/time in UTC format encoded as
		 *   an ISO 8601 date string like '2010-12-31T15:30:59+00:00' or '2010-12-31T15:30:59Z'
		 * passwordDigest looks sg like 'MDBhOTMwZGE0OTMxMjJlODAyNmE1ZWJhNTdmOTkxOWU4YzNjNWZkMw=='
		 */		
		$nonce = $this->getRandomBytes();
		$timestamp = gmdate("c");
		
		$passwordDigest = base64_encode(sha1($nonce . $timestamp . $this->_secret, false));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'X-WSSE: UsernameToken ' .
				'Username="'.$this->_username.'", ' .
				'PasswordDigest="'.$passwordDigest.'", ' .
				'Nonce="'.$nonce.'", ' .
				'Created="'.$timestamp.'"',
				'Content-type: application/json;charset="utf-8"',
			)
		);
		
		$response = curl_exec($ch);		
		curl_close($ch);
		
		$parts = preg_split("@\r?\n\r?\nHTTP/@u", $response);
		$parts = (count($parts) > 1 ? 'HTTP/' : '').array_pop($parts);
		list($headers, $output) = preg_split("@\r?\n\r?\n@u", $parts, 2);
		
		print_r($output);
	}
	
	public function getRandomBytes() {
		if (function_exists('random_bytes')) {
			$salt = base_convert(bin2hex(random_bytes(64)), 16, 36);	
		} else {
			$salt = hash('sha256', time() . mt_rand());
		}
		
		$key = substr($salt, 0, 32);
		return $key;
	}

}
