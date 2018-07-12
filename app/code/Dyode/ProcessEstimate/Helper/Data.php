<?php

namespace Dyode\ProcessEstimate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID) {
		
		$soapClient = $this->setSoapClient(); 
		$soapResponse = $soapClient->WebDownPayment(array('CustID' => $customerId, 'Amount' => $amountPaid, 'InvNo' => $invoiceNumber, 'ReferID' => $referenceID));

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
