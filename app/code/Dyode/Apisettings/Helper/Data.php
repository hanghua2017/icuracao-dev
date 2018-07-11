<?php
/*
Date: 03/07/2018
Author :Kavitha
*/

namespace Dyode\Apisettings\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /*=== Get customer information from AR by using customer ID ===*/
    public function getARCustomerInfoAction($cu_account){

        if(!isset($cu_account)){return false; }
        if(!is_numeric($cu_account)){return false;}

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
        $soapResponse = $soapClient->GetCustomerContact(array('cust_id' => $cu_account));
             
        $result = json_decode($soapResponse->GetCustomerContactResult);
        
        var_dump($result);

        if($result->OK == false){
           // return false;
        }
        
        $custInfo = json_decode(json_encode($result->DATA),true);

       // return $custInfo;

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
        return $this->getConfig('linkaccount/curacao/apiuser');
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


}
?>