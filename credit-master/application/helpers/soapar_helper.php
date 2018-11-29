<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('soaparcall')){
  function soaparcall($data,$wsname){
    $serverIps = array("127.0.0.1","10.0.0.44","10.0.0.41","10.0.0.42","10.0.0.43","10.0.0.45","10.0.0.117","10.0.0.118","10.0.0.119","10.0.0.120","167.114.1.118");
    
    if(in_array($_SERVER['SERVER_ADDR'],$serverIps)){
            $link = 'https://exchangeweb.lacuracao.com:2007/ws1/test/eCommerce/Main.asmx?WSDL';
    }else{
            $link = 'https://exchangeweb.lacuracao.com:2007/ws1/eCommerce/Main.asmx?WSDL';
    }
    
    $ns = 'http://lacuracao.com/WebServices/eCommerce/';
    $headers = array('trace' => 1,'exceptions'=>1, 'encoding'=>'UTF-8', 'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
    
    try {
      $client = new SoapClient($link,$headers);
    } catch (\SoapFault $e) {
      echo "<h1>".lang('preapp_webservice_error')."</h1>";
      $this->session->set_flashdata('error_message',null);
      log_message('error',$e);
      return false;
    }
    $headerbody = array('UserName' => 'mike','Password' => 'ecom12');
    // Create Soap Header.
    $header = new SOAPHeader($ns, 'TAuthHeader', $headerbody);

    // Set the Headers of Soap Client.
    $h = $client->__setSoapHeaders($header);

    // Call the soap method.
    $result = $client->__soapCall($wsname, array($data));
    
    return $result;
    
    $s = json_encode((array) $result);
      
    // if (!is_soap_fault($result)) {
    //     header('Content-type: application/json');
    //     echo $s;
    // }
    return $s;
  }
}