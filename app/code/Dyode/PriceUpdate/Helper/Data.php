<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Helper;

/**
 * Price Helper
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
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
     * function name : setStock
     * definition : api call to set stock in AR
     * @return no return
     */
    public function setStock($productSkuList, $module)
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
            $request->setUri($apiUrl."SetEcommerceStock");
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
            $this->addLogs('SetEcommerceStock API calling', 'response received ', $module);
        }
        catch (\Exception $e) {
        	$this->addLogs('SetEcommerceStock API calling', 'response failed '.$e, $module);
            return false;
        }
    }

    /**
     * function name : addLogs
     * definition : write audit logs
     * @return no return
     */
    public function addLogs($action, $description, $module)
    {
    	$clientIP = $_SERVER['REMOTE_ADDR'];
	    $this->auditLog->saveAuditLog([
            'user_id' => 'admin',
            'action' => $action,
            'description' => $description,
            'client_ip' => $clientIP,
            'module_name' => $module
	    ]);
    }    
}  