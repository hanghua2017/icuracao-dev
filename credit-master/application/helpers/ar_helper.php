<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('arcall')){
  function arcall($customer,$wsname){
    
    $serverIps = array("127.0.0.1","10.0.0.44","10.0.0.41","10.0.0.42","10.0.0.43","10.0.0.45","10.0.0.117","10.0.0.118","10.0.0.119","10.0.0.120","167.114.1.118");
    
    if(in_array($_SERVER['SERVER_ADDR'],$serverIps)){
             $url = 'https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/';
             $key = 'TEST-WNNxLUjBxA78J7s';
    }else{
              $url = 'https://exchangeweb.lacuracao.com:2007/ws1/restapi/ecommerce/';
              $key = 'PROD-T8VtT5GgM7t97Ua';
    }
    
    $data = json_encode( $customer, JSON_FORCE_OBJECT );
    
    $context = array(
          'http' => array(
                  'method'    =>  'PUT',
                  'header'    =>  "x-api-key: ".$key."\r\nContent-Length: " . strlen( $data ) . "\r\nContent-Type: application/json\r\n",
                  'content'   =>  $data
              )                             
        );

      $context = stream_context_create( $context );
      $url = $url.$wsname;
      $result = file_get_contents( $url, false, $context );

      return json_decode($result);    
  }
}