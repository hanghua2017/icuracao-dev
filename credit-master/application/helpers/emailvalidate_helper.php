<?php 

if(!function_exists('emailValidate')){
  function emailValidate($email){
    $LicenseKey = 'WS73-PKZ1-VCQ1';
    $URL = "http://ws.serviceobjects.com/ev3/api.svc/ValidateEmailInfo/Full/".urlencode($email)."/".urlencode($LicenseKey)."?format=json";
   
    // Get cURL resource
    $curl = curl_init();
    curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $URL, CURLOPT_USERAGENT => 'Service Objects Email Validation 3'));
    curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($resp, TRUE)), RecursiveIteratorIterator::SELF_FIRST);
    $emailvalidataion = '0';
    foreach ($jsonIterator as $key => $val)
    {
      if(is_array($val))
      {
        if($val['Score']<3){
          $emailvalidataion = '1';
          break;
        }else{
          $emailvalidataion = '0';
          break;
        }
      } 
    }
    return $emailvalidataion;
  }
}