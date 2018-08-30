<?php

namespace Dyode\Signifyd\Helper;

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

	
}
