<?php
namespace Dyode\Customerstatus\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
  //Check status of a customer
  public function checkCustomerStatus($customerId)
  {
    $Customer_Status = $this->isCustomerActive($customerId);
    if($Customer_Status == 'SOFT' || $Customer_Status == 'NO'){
      //reactivate a customer
      $this->reActivateAccount($customerId);
    }else {
      //shipping address validation
      $this->validateAddress($customerId);
    }
  }

  //Reactivate a customer account
  public function reActivateAccount($customerId)
  {
    $Result_code = $this->estimateOk($customerId);
    //set Customer_status if result_code is 0
    if ($Result_code == '0') {
      $Customer_Status = 'Yes';
      $this->validateAddress($customerId);
    } else {
      // Cancel order with reason 'UA-014'
    }
  }

  //validate shipping address with default customer address from AR
  public function validateAddress($customerId)
	{
    $shipping_street = '3325 W PICO BLVD APT 9';
    $shipping_zip = '90019';
    //Get customer address from AR
    $defaultCustomerAddress = $this->getCustomerContact($customerId);
    $defaultZip = substr($defaultCustomerAddress->ZIP, 0, 5);
    $defaultStreet = $defaultCustomerAddress->STREET;
    if($shipping_street == $defaultStreet && $shipping_zip == $defaultZip){
      //Mark Address in Magento as valid
    } else {
      //Set Address Mismatch
      $Address_Mismatch = true;
    }
	}

  //API for getting customer contactaddress
  public function getCustomerContact($customerId)
	{
    $soapClient = $this->setSoapClient();
    $soapResponse = $soapClient->GetCustomerContact(array('cust_id' => $customerId));
    $response = json_decode($soapResponse->GetCustomerContactResult);
    $customerinfo = json_decode(json_encode($response->DATA));
    return $customerinfo; //return customer address
	}

  //API for checking whether a customer account is active
  public function isCustomerActive($customerId)
    {
      $soapClient = $this->setSoapClient();
      $soapResponse = $soapClient->IsCustomerActive(array('CustomerID' => $customerId));
      $returnValue = explode(";",$soapResponse->IsCustomerActiveResult);
      return $returnValue[0]; //return account status
    }

    //API for reactivating a customer account
    public function estimateOk($customerId)
    {
      $soapClient = $this->setSoapClient();
      $soapResponse = $soapClient->EstimateOk(array('cust_id' => $customerId));
      $response = json_decode($soapResponse->EstimateOkResult);
      $returnValue = $response->CODE;
      return $returnValue; //return code value
    }

    //setting Soapclient
    public function setSoapClient()
    {
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
