<?php

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
    function to connect the AR using SOAP
    */

    public function arConnect(){
        $userName =  $this->getApiuser();
        $password =  $this->getApipass();
        $headerbody =  array("UserName" => $userName, "Password" => $password);

        $wsdlUrl = $this->getWsdlUrl();
        $soapClient = new \SoapClient($wsdlUrl,['version' => SOAP_1_2]);
        $xmlUrl = $this->getApiUrl();

        //Create Soap Header.
        $header = new \SOAPHeader($xmlUrl, 'TAuthHeader', $headerbody);
        //set the Headers of Soap Client.
        $soapHeader = $soapClient->__setSoapHeaders($header);
        $this->soapClient = $soapClient;
        return $soapClient;
    }

    /*=== Get customer information from AR by using customer ID ===*/
    public function getARCustomerInfoAction($cu_account){

        if(!isset($cu_account)){return false; }
        if(!is_numeric($cu_account)){return false;}

        $soapClient =  $this->arConnect();
        $soapResponse = $soapClient->GetCustomerContact(array('cust_id' => $cu_account));

        $result = json_decode($soapResponse->GetCustomerContactResult);

        if($result->OK == false){
           return false;
        }

        $custInfo = json_decode(json_encode($result->DATA),true);

        return $custInfo;

    }
    /*=== Validate Customer Information and get DownPayment ===*/
    public function verifyPersonalInfm($customerDetails){
        $soapClient =  $this->arConnect();
        $soapResponse = $soapClient->ValidateDP($customerDetails);


        $verifiedResult = $soapResponse->ValidateDPResult;

        if($verifiedResult->StatusMessage != 'OK'){
            return false;
        }

        return $verifiedResult;
    }

    /*=== Function to send the verification code ===*/
    public function phoneVerifyCode($_phonenumber, $_times, $_type)
    {
          $salt = 'ag#A\J9.u=j^v}X3';
          $code = rand(10000,  99999);
          $url = '';

          if($_type===1)
          {
                  $wsdlUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');
                  $params['CountryCode'] = '1';
                  $params['PhoneNumber'] = $_phonenumber;
                  $params['LicenseKey'] = $this->getConfig('linkaccount/curacao/licensekey');
                  $params['CallerID'] = $this->getConfig('linkaccount/curacao/callerid');
                  $params['Language'] = 'en';
                  $params['VerificationCode'] = $code;
                  $params['Extension'] = '';
                  $params['ExtensionPauseTime'] = '';

                //  $soapClient = new \SoapClient($wsdlUrl,['version' => SOAP_1_2]);

                  $soapClient = new \SoapClient($wsdlUrl, array( "trace" => 1 ));
                  $response = $soapClient->PlaceCall($params);
                  $result= $response->PlaceCallResult;
                  if (isset($result->Error))
                  {
                          return -1;
                  }
          }
          else
          {
                  $wsdlUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');
                  $params['CountryCode'] = '1';
                  $phone = $_phonenumber;
                  $valuesToDelete = array('(', ')', '-', ' ');
                  $phone = str_replace($valuesToDelete, '', $phone);
                  $params['PhoneNumber'] = $phone;
                  $params['LicenseKey'] = $this->getConfig('linkaccount/curacao/licensekey');
                  $params['Message'] = 'Your Curacao verification code is ' . $code . '.';
                  $soapClient = new \SoapClient($wsdlUrl, array("trace" => 1));
                  $response = $soapClient->SendSMS($params);
                  $result = $response->SendSMSResult;
                  if (isset($result->Error))
                  {
                          return -1;
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
}
?>
