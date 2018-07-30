<?php

namespace Dyode\ProcessEstimate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	//webDownPayment API function
	public function webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID) {
		
		$customerID = '53208833';
   		$amountPaid = 1.5;
   		$invoiceNumber = 'ZEP58P4';
   		$referenceID = 'refer#1';
   		$dataSetItems = array('cust_id' => $customerID,'amount' => $amountPaid, 'inv_no' => $invoiceNumber, 'referID' => $referenceID);
   		$response = $this->setAPIClient('webDownpayment',$dataSetItems);
        //var_dump($response);exit;

	}

	//SupplyInvoice API function
	public function goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email) {
		$firstName = 'Joe';
   		$email = 'joe@smith.com';
   		$invoiceNumber = 'ZEP58P6';
   		$lastName = 'Smith';
   		$dataSetItems = array('InvNo' => $invoiceNumber,'FirstName' => $firstName, 'LastName' => $lastName, 'eMail' => $email);
        $response = $this->setAPIClient('SupplyInvoice',$dataSetItems);
        //var_dump($response);exit;
	}

	//Setting REST API Client
	public function setAPIClient($endPoint, $dataSetItems) {
		$ch = curl_init("https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/".$endPoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataSetItems));
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Api-Key: TEST-WNNxLUjBxA78J7s"));
        $result = curl_exec($ch);
		return $result;
	}	
}
