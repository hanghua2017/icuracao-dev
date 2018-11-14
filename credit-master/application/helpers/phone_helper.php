<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('phonecode')){
   function phonecode($_phonenumber, $_type)
    { 
            $salt = 'ag#A\J9.u=j^v}X3';
            $code = rand(100000,  999999);
            $url = '';

            if($_type==='call')
            {
                    $wsdlUrl = "https://ws.serviceobjects.com/tv/TelephoneVerification.asmx?WSDL";
                    $params['CountryCode'] = '1';
                    $params['PhoneNumber'] = $_phonenumber;
                    $params['LicenseKey'] = 'WS58-UYM3-JXS1';
                    $params['CallerID'] = '8664101611';
                    $params['Language'] = 'en';
                    $params['VerificationCode'] = $code;
                    $params['Extension'] = '';
                    $params['ExtensionPauseTime'] = '';

                    $soapClient = new SoapClient($wsdlUrl, array( "trace" => 1 ));
                    $response = $soapClient->PlaceCall($params);
                    $result= $response->PlaceCallResult;
                    if (isset($result->Error)) 
                    {
                            return -1;
                    }
            }
            else
            {
                    $wsdlUrl = "https://ws.serviceobjects.com/tv/TelephoneVerification.asmx?WSDL";
                    $params['CountryCode'] = '1';
                    $phone = $_phonenumber;
                    $valuesToDelete = array('(', ')', '-', ' ');
                    $phone = str_replace($valuesToDelete, '', $phone);
                    $params['PhoneNumber'] = $phone;
                    $params['LicenseKey'] = 'WS58-UYM3-JXS1';
                    $params['Message'] = 'Your Curacao verification code is ' . $code . '.';
                    $soapClient = new SoapClient($wsdlUrl, array("trace" => 1));
                    $response = $soapClient->SendSMS($params);
                    $result = $response->SendSMSResult;
                    if (isset($result->Error)) 
                    {
                            return -1;
                    }
            }

            return trim(md5($salt . $code));	
    }  
}

if(!function_exists('verifycode')){
  // Verify code return 0 is verified
 function verifycode($hash, $code){
      $salt = 'ag#A\J9.u=j^v}X3';
      
      if(trim($hash) === trim(md5($salt . $code))) {
          return true;
      }
          
      return false;
  }
}

if(!function_exists('ArFormat')){
    function ArFormat($number){
        $area = substr($number, 0,3);
        $middle = substr($number, 3,3);
        $last = substr($number, 6,4);
           
        return '('.$area . ')'.$middle.'-'.$last;
    }
}