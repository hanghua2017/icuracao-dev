<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('adminToken')){
   function adminToken(){
    $domain = $_SERVER['SERVER_NAME'];
    $magento_admin_username = MAGENTO_ADMIN_USERNAME;
    $magento_admin_password = MAGENTO_ADMIN_PASSWORD;
    $userData = array("username" => $magento_admin_username, "password" => $magento_admin_password);
    $ch = curl_init("http://$domain/rest/V1/integration/admin/token");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

    $token = curl_exec($ch);
    return $token;
  }
}