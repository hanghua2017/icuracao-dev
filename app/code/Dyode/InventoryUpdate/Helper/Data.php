<?php

namespace Dyode\InventoryUpdate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function __construct(
		\Dyode\PriceUpdate\Helper\Data $priceHelper,	
		\Dyode\ARWebservice\Helper\Data $apiHelper 
	) {
	    $this->apiHelper = $apiHelper;
	    $this->priceHelper = $priceHelper;
	}

	/**
     * function name : getStock
     * definition : get Stock from AR
     * @return return stock data
     */
	public function getStock() {
		try{
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
			   'timeout' => 360
			];
			$client->setOptions($options);
			$this->priceHelper->addLogs('getStock API Calling', $request, 'dyode_inventoryupdate');
			$response = $client->send($request);
			$data = $response->getBody();
			$this->priceHelper->addLogs('getStock API Calling', $response->getBody(), 'dyode_inventoryupdate');
			return $data;
		}catch (\Exception $exception) {
	        $this->priceHelper->addLogs('getStock API Calling', 'failed'.$exception, 'dyode_inventoryupdate');
	    }
	}
}
