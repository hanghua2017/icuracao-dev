<?php
namespace Dyode\Customerstatus\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
  //Check status of a customer
  public function checkCustomerStatus($customerId)
  {
    $Customer_Status = $this->isCustomerActive($customerId);
    var_dump($Customer_Status);exit;
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
      $Address_Mismatch = false;
    } else {
      //Set Address Mismatch
      $Address_Mismatch = true;
    }
	}

  //API for getting customer contactaddress
  public function getCustomerContact($customerId)
	{
    $httpHeaders = new \Zend\Http\Headers();
    $httpHeaders->addHeaders([
       'Accept' => 'application/json',
       'Content-Type' => 'application/json',
       'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
    ]);

    $request = new \Zend\Http\Request();
    $request->setHeaders($httpHeaders);
    $request->setUri("https://exchangeweb.lacuracao.com:2007/ws2/test/restapi/ecommerce/GetCustomerContact?cust_id=$customerId");
    $request->setMethod(\Zend\Http\Request::METHOD_GET);

    $client = new \Zend\Http\Client();
    $options = [
       'adapter'   => 'Zend\Http\Client\Adapter\Curl',
       'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
       'maxredirects' => 0,
       'timeout' => 30
    ];
    $client->setOptions($options);
    $response = $client->send($request);
    $data = $response->getBody();
    echo "<pre>";
    print_r(json_decode($data));exit;
	}

  //API for checking whether a customer account is active
  public function isCustomerActive($customerId)
    {
      $httpHeaders = new \Zend\Http\Headers();
      $httpHeaders->addHeaders([
         'Accept' => 'application/json',
         'Content-Type' => 'application/json',
         'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
      ]);

      $request = new \Zend\Http\Request();
      $request->setHeaders($httpHeaders);
      $request->setUri("https://exchangeweb.lacuracao.com:2007/ws2/test/restapi/ecommerce/getStock?top50=false");
      $request->setMethod(\Zend\Http\Request::METHOD_GET);

      $client = new \Zend\Http\Client();
      $options = [
         'adapter'   => 'Zend\Http\Client\Adapter\Curl',
         'curloptions' => [CURLOPT_FOLLOWLOCATION => true]
      ];
      $client->setOptions($options);
      $response = $client->send($request);
      $data = $response->getBody();
      echo "<pre>";
      print_r(json_decode($data));exit;
    }

    //API for reactivating a customer account
    public function estimateOk($customerId)
    {
      $httpHeaders = new \Zend\Http\Headers();
      $httpHeaders->addHeaders([
         'Accept' => 'application/json',
         'Content-Type' => 'application/json',
         'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
      ]);

      $request = new \Zend\Http\Request();
      $request->setHeaders($httpHeaders);
      $request->setUri("https://exchangeweb.lacuracao.com:2007/ws2/test/restapi/ecommerce/EstimateOk?cust_id=$customerId");
      $request->setMethod(\Zend\Http\Request::METHOD_GET);

      $client = new \Zend\Http\Client();
      $options = [
         'adapter'   => 'Zend\Http\Client\Adapter\Curl',
         'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
         'maxredirects' => 0,
         'timeout' => 30
      ];
      $client->setOptions($options);
      $response = $client->send($request);
      $data = $response->getBody();
      echo "<pre>";
      print_r(json_decode($data));exit;
    }
}
