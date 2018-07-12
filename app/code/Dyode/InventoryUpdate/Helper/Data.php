<?php

namespace Dyode\InventoryUpdate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function batchGetInventory($skuList,$location) {
		
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->BatchGetInventory(array('SkuList' => $skuList, 'location' => $location));
		var_dump($soapResponse);exit;
	}

	public function goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email) {
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->GoSupplyInvoice(array('InvNo' => $invoiceNumber, 'FirstName' => $firstName, 'LastName' => $lastName, 'eMail' => $email));
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
