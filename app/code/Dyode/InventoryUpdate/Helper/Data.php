<?php

namespace Dyode\InventoryUpdate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function __construct(
	\Dyode\ARWebservice\Helper\Data $apiHelper 
	) {
	    $this->apiHelper = $apiHelper;
	}


	public function getStock() {
		$apiKey = $this->apiHelper->getApiKey();
        $apiUrl = $this->apiHelper->getApiUrl();
		$httpHeaders = new \Zend\Http\Headers();
		$httpHeaders->addHeaders([
		   'Accept' => 'application/json',
		   'Content-Type' => 'application/json',
		   'X-Api-Key' => $apiKey
		]);

		$request = new \Zend\Http\Request();
		$request->setHeaders($httpHeaders);
		$request->setUri($apiUrl."getStock?top50=false");
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
