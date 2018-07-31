<?php
/*
Date: 03/07/2018
Author :Kavitha
*/

namespace Dyode\ARWebservice\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /*
    To retrieve the API user in system configuration
    */
    public function getApiUser(){
        return $this->getConfig('linkaccount/curacao/apiuser');
    }
    /*
    To retrieve the API Password in system configuration
    */
    public function getApiPass(){
        return $this->getConfig('linkaccount/curacao/apipass');
    }
    /*
    To retrieve the WSDL Url in system configuration
    */
    public function getWsdlUrl(){
        return $this->getConfig('linkaccount/curacao/wsdlurl');
    }
    /*
    To retrieve the API Url in system configuration
    */
    public function getApiUrl(){
        return $this->getConfig('linkaccount/curacao/apiurl');
    }
    /*
    To retrieve the REST API Key in system configuration
    */
    public function getApiKey(){
        return $this->getConfig('linkaccount/curacao/apikey');
    }

    /*
    * function to connect the AR using REST
    * $fnName = fucntion namespace
    * type = GET/POST
    * $params as array
    */
    public function arConnect($fnName,$type,$params){
        $apiUrl  = $this->getApiUrl();
        $apiKey  = $this->getApiKey();
        if(!empty($params)){
          $paramVal = '?';
          $cnt = 0;
          foreach($params as $key=>$value){
            if($cnt != 0 || $cnt != (count($params)-1))
              $paramVal .= $key."=".$value;
            else
              $paramVal .= $key."=".$value."&";
            $cnt++;
          }
        }
        $curlUrl = $apiUrl."/".$fnName.$paramVal;

        $ch = curl_init($curlUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Api-Key:".$apiKey));

        $result = curl_exec($ch);
        return $result;
    }

    /*=== Get customer information from AR by using customer ID ===*/
    public function getARCustomerInfoAction($cu_account){

        if(!isset($cu_account)){return false; }
        if(!is_numeric($cu_account)){return false;}
        $params = array('cust_id' => $cu_account);

        $restResponse =  $this->arConnect('GetCustomerContact', 'GET',$params);
        $result = json_decode($restResponse);
        if($result->OK != 1){
           return false;
        }
        $custInfo = $result->DATA;
        return $custInfo;

    }
    /*=== Validate Customer Information and get DownPayment ===*/
    public function verifyPersonalInfm($customerDetails){

      $restResponse =  $this->arConnect('ValidateDP', 'GET',$customerDetails);
      $result = json_decode($restResponse);
      if($result->OK != true){
         return false;
      }
      $verifiedResult = $result->DATA;
      return $verifiedResult;

    }

    /*=== Function to send the verification code ===*/
    /* $type 0 -> Send code as text
    *        1-> Send code as Voice
    */
    public function phoneVerifyCode($_phonenumber, $_times, $_type)
    {
          $salt = 'ag#A\J9.u=j^v}X3';
          $code = rand(10000,  99999);
          $url = '';

          if($_type == 1 )
          {
                  $wsdlUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');
                  $countryCode = '1';
                  $phone = $_phonenumber;
                  $valuesToDelete = array('(', ')', '-', ' ');
                  $phone = str_replace($valuesToDelete, '', $phone);
                  $phoneNumber = $phone;
                  $licenseKey = $this->getConfig('linkaccount/curacao/licensekey');
                  $callerID = $this->getConfig('linkaccount/curacao/callerid');
                  $language = 'en';
                  $verifyCode = $code;
                  $extension = '';
                  $extensionPauseTime = '';

                //  $soapClient = new \SoapClient($wsdlUrl,['version' => SOAP_1_2]);

                  $URL = $apiUrl.'SMS/'.$countryCode.'/'.$phoneNumber.'/'.$extension.'/'.$extensionPauseTime.'/'.$verifyCode.'/'.$callerID.'/'.$language.'/'.$licenseKey.'?format=json';
                  // Get cURL resource
                  $curl = curl_init();
                  curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $URL, CURLOPT_USERAGENT => 'Service Objects Telephone Verification'));
                  curl_setopt($curl, CURLOPT_TIMEOUT, 50); //timeout in seconds
                  // Send the request & save response to $resp
                  $resp = curl_exec($curl);

                  if($resp == false)
                  {
                      curl_close($curl);
                      return -1;
                  }

          }
          else
          {
                  $apiUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');
                  $countryCode = '1';
                  $phone = $_phonenumber;
                  $valuesToDelete = array('(', ')', '-', ' ');
                  $phone = str_replace($valuesToDelete, '', $phone);
                  $phoneNumber = $phone;
                  $licenseKey = $this->getConfig('linkaccount/curacao/licensekey');
                  $message = 'Your Curacao verification code is ' . $code . '.';

                  //use backup url once given purchased license key
                  $backupURL = $this->getConfig('linkaccount/curacao/backupurl')."SendSMS?CountryCode=".urlencode($CountryCode)."&PhoneNumber=".urlencode($PhoneNumber)."&Message=".urlencode($Message)."&LicenseKey=".urlencode($LicenseKey);

                  //$URL = $apiUrl."SendSMS?CountryCode=".urlencode($countryCode)."&PhoneNumber=".urlencode($phoneNumber)."&Message=".urlencode($message)."&LicenseKey=".urlencode($licenseKey);

                  $URL = $apiUrl.'SMS/'.$countryCode.'/'.$phoneNumber.'/'.$message.'/'.$licenseKey.'?format=json';
                  // Get cURL resource
                  $curl = curl_init();
                  curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $URL, CURLOPT_USERAGENT => 'Service Objects Telephone Verification'));
                  curl_setopt($curl, CURLOPT_TIMEOUT, 50); //timeout in seconds
                  // Send the request & save response to $resp
                  $resp = curl_exec($curl);
                  // Close request to clear up some resources
                  if($resp == false)
                  {
                      curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $backupURL, CURLOPT_USERAGENT => 'Service Objects Telephone Verification'));
                      curl_setopt($curl, CURLOPT_TIMEOUT, 50); //timeout in seconds
                      // Send the request & save response to $resp
                      $resp = curl_exec($curl);
                      if($resp == false)
                      {
                          curl_close($curl);
                          return -1;
                      }
                 }
          }
          return trim(md5($salt . $code));
  }

  // Verify code return 0 is verified
  public function verifyCode($_enc, $_vid)
  {
      $salt = 'ag#A\J9.u=j^v}X3';
      if(trim($_enc) === trim(md5($salt . $_vid)))
          return 0;
      else
          return -1;
  }
  /*==== Function to return the credit limit ===*/
  public function getCreditLimit($cu_account){
    if(!isset($cu_account)){return false; }
    if(!is_numeric($cu_account)){return false;}

    $params = array('cust_id' => $cu_account);

    $restResponse =  $this->arConnect('GetCustomerCreditLimit', 'GET',$params);
    $result = json_decode($restResponse);

    if($result->OK != 1){
       return false;
    }

    return $result->DATA;
  }

}
?>
