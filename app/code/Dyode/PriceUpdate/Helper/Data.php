<?php

namespace Dyode\PriceUpdate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	//get price list of all products
	public function batchGetPrice($skulist,$location){
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->BatchGetPrice(array('SkuList' => $skulist, 'location' => $location));
		return $soapResponse;
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
