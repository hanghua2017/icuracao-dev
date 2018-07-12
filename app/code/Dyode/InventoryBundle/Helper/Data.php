<?php

namespace Dyode\InventoryBundle\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function getSetItems($productSKU) {
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->GetSetItems(array('item_id' => $productSKU));
		var_dump($soapResponse);exit;
	
	    $arSetDescription = json_decode($soapResponse->GetSetItemsResult);	  
		return $arSetDescription;
	}

	public function inventoryLevel($productSKU,$storeLocation) {
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->InventoryLevel(array('cItem_ID' => $productSKU, 'cLocations' => $storeLocation));
				var_dump($soapResponse);exit;
	}

	public function setSoapClient() {
	$wsdlUrl = 'https://exchangeweb.lacuracao.com:2007/ws1/test/ecommerce/Main.asmx?WSDL';
		$soapClient = new \SoapClient($wsdlUrl,['version' => SOAP_1_2]);
		$xmlns = 'http://lacuracao.com/WebServices/eCommerce/';
		$headerbody = array('UserName' => 'mike',
		  'Password' => 'ecom12');
		//Create Soap Header.
		$header = new \SOAPHeader($xmlns, 'TAuthHeader', $headerbody);
		//set the Headers of Soap Client.
		$soapHeader = $soapClient->__setSoapHeaders($header);
		return $soapClient;
	}	
}
