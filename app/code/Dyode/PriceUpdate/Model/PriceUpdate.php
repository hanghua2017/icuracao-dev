<?php
namespace Dyode\PriceUpdate\Model;

use \Magento\Framework\Model\AbstractModel;

class PriceUpdate extends \Magento\Framework\View\Element\Template {

	protected $_productCollectionFactory;

	protected $_curl;

	public $data = array();

	public $skuList = array();

	public $location = '81';

	public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
	\Dyode\PriceUpdate\Helper\Data $helper,
	\Magento\Framework\HTTP\Client\Curl $curl,
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;
	    $this->_helper = $helper;
	    $this->_curl = $curl;
	    parent::__construct($context, $data);
	}

	public function updatePrice() {
		//$this->getProductSkulist();
		$this->getBatchPrice();
		var_dump("expression");exit;

	} 

	//get Sku list of all products
	public function getProductSkulist(){
		$productCollection = $this->_productCollectionFactory->create();
	    /** Apply filters here */
	    $productCollection->addAttributeToSelect('*');

	    foreach ($productCollection as $product){
	        $sku = utf8_encode(strtoupper(trim($product->getSku())));
			//$this->data[] = "$sku";
			$this->skuList[$sku]['entity_id'] = $product->getId();
	    }  
	} 

	public function getBatchPrice(){

		$httpHeaders = new \Zend\Http\Headers();
		$httpHeaders->addHeaders([
		   'Accept' => 'application/json',
		   'Content-Type' => 'application/json',
		   'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
		]);

		$request = new \Zend\Http\Request();
		$request->setHeaders($httpHeaders);
		$request->setUri('https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/getPrices?top50=true');
		$request->setMethod(\Zend\Http\Request::METHOD_GET);

		$params = new \Zend\Stdlib\Parameters([
		   'top50' => true
		]);
		//$request->setQuery($params);

		$client = new \Zend\Http\Client();
		$options = [
		   'adapter'   => 'Zend\Http\Client\Adapter\Curl',
		   'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
		   'maxredirects' => 0,
		   'timeout' => 30
		];
		$client->setOptions($options);

		$response = $client->send($request);
		$data = json_decode($response->getBody());
		$list = $data->LIST;
		echo '<pre>';
		//var_dump($list);exit;
		foreach ($list as $key) {
			echo '<pre>';
			echo $key->price->COST;
		}

		// $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/getPrices?top50=true";
		// $this->_curl->addHeader("Content-Type: application/json", "X-Api-Key: TEST-WNNxLUjBxA78J7s");
		// $test = $this->_curl->get($url);
		// $response = $this->_curl->getBody();
		// var_dump($test);exit;
		// $this->data[0] = '21 -N13-5000';
		// $this->data[1] = '42C-F42-MARVISTA/6PC';
		// $this->data[2] = '48B-M25-4501/BK';
		// $this->data[3] = '21 -M35-HY/HME';
		// $this->data[4] = '21B-N77-82471795';
		// $items = array_chunk($this->data, 100);

		// foreach ($items as $item) {
		// 	$sku = implode(';', $item);
		// 	$batchPrice = $this->_helper->batchGetPrice($sku,$this->location);

		// 	$this->processBatchprice($batchPrice);
		// }
		// var_dump("expression");exit;
	} 

	public function processBatchprice($priceObject){
		$pricelist = json_decode($priceObject->BatchGetPriceResult, true);
		$items = $pricelist['SKULIST'];
		foreach ($items as $item){
				$obj = array( 
					'err' => strtoupper(trim( $item[0] )),
					'price' => $item[1],
					'special_price' => $item[2],
					'rebate' => $item[3],
					'recycling_price' => $item[4],
					'recycling_description' => trim( $item[5] ),
					'cost' => $item[6],
					'msrp' => $item[7],
					'unknown' => $item[8],	
					'sku' => utf8_encode(trim( $item[9] )),
					'iqi' => trim( $item[10] ),
					'ar_status' => trim( $item[11] )	
				);

				$this->skus[ $obj['sku'] ]= $obj;
			}
	}
}
