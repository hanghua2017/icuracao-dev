<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\SetEcommerceData\Helper;

/**
 * SetEcommerceData Helper
 * @category Dyode
 * @package  Dyode_SetEcommerceData
 * @module   SetEcommerceData
 * @author   Nithin
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	/*
	 * helper constructer
	 */ 
	public function __construct(
	   	\Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog,	
      	\Dyode\ARWebservice\Helper\Data $apiHelper 
    ) {
      $this->apiHelper = $apiHelper;
      $this->auditLog = $auditLog;
    }

    /**
     * function name : setEcommerceData
     * definition : api call to set product status in AR
     * @return no return
     */
    public function setEcommerceStock($productSkuList, $delta)
    {
    	$apiKey = $this->apiHelper->getApiKey();
      	$apiUrl = $this->apiHelper->getApiUrl();
      	$postString = array('list' => $productSkuList);
        try {

            $httpHeaders = new \Zend\Http\Headers();
            $httpHeaders->addHeaders([
               'Accept' => 'application/json',
               'Content-Type' => 'application/json',
               'X-Api-Key' => $apiKey
            ]);
            $request = new \Zend\Http\Request();
            $request->setHeaders($httpHeaders);
            $request->setUri($apiUrl."SetEcommerceStock?deltas=$delta");
            $request->setMethod(\Zend\Http\Request::METHOD_PUT);

            $client = new \Zend\Http\Client();
            $options = [
               'adapter'   => 'Zend\Http\Client\Adapter\Curl',
               'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
               'maxredirects' => 0,
               'timeout' => 360
            ];
            $client->setOptions($options);
  			$request->setContent(json_encode($postString));
            $response = $client->send($request);
        }
        catch (\Exception $e) {
            return false;
        }
    } 
}  