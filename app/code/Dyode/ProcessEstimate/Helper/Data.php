<?php

namespace Dyode\ProcessEstimate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function __construct(
      \Dyode\ARWebservice\Helper\Data $apiHelper,
      \Dyode\PriceUpdate\Helper\Data $logHelper 
  ) {
      $this->apiHelper = $apiHelper;
      $this->logHelper = $logHelper;
  }

  //webDownPayment API function
	public function webDownPayment($customerID,$amountPaid,$invoiceNumber,$referenceID) {
 		$dataSetItems = array('cust_id' => $customerID,'amount' => $amountPaid, 'inv_no' => $invoiceNumber, 'referID' => $referenceID);
 		$response = $this->setAPIClient('webDownpayment',$dataSetItems);
    $this->logHelper->addLogs('Calling weDownpayment', json_encode($response), 'Dyode_Processestimate');

	}

	//SupplyInvoice API function
	public function goSupplyInvoice($invoiceNumber,$firstName,$lastName,$email) {
 		$dataSetItems = array('InvNo' => $invoiceNumber,'FirstName' => $firstName, 'LastName' => $lastName, 'eMail' => $email);
    $response = $this->setAPIClient('SupplyInvoice',$dataSetItems);
    $this->logHelper->addLogs('Calling goSupplyInvoice', json_encode($response), 'Dyode_Processestimate');
	}

	//Setting REST API Client
	public function setAPIClient($endPoint, $dataSetItems) {
    $apiKey = $this->apiHelper->getApiKey();
    $apiUrl = $this->apiHelper->getApiUrl();
		$ch = curl_init($apiUrl.$endPoint);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataSetItems));
  	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Api-Key: $apiKey"));
      $result = curl_exec($ch);
		return $result;
	}	
}
