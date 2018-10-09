<?php

namespace Dyode\SetInventory\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function getSetItems($productSKU) {
		$httpHeaders = new \Zend\Http\Headers();
		$httpHeaders->addHeaders([
		   'Accept' => 'application/json',
		   'Content-Type' => 'application/json',
		   'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
		]);

		$request = new \Zend\Http\Request();
		$request->setHeaders($httpHeaders);
		$request->setUri("https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/getSetItems?item_id=$productSKU");
		$request->setMethod(\Zend\Http\Request::METHOD_GET);

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
		return $data;
	}

	public function inventoryLevel($productSKU,$storeLocations) {
		$httpHeaders = new \Zend\Http\Headers();
		$httpHeaders->addHeaders([
		   'Accept' => 'application/json',
		   'Content-Type' => 'application/json',
		   'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
		]);

		$request = new \Zend\Http\Request();
		$request->setHeaders($httpHeaders);
		$request->setUri("https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/InventoryLevel?item_id=$productSKU&locations=$storeLocations");
		$request->setMethod(\Zend\Http\Request::METHOD_GET);

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
		return $data;
	}
}
