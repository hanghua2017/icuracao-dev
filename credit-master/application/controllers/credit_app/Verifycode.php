<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifycode extends CI_Controller{
  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    $data = $this->input->post();
    if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' || empty($data) ||  $data['uuid'] == '' || $data['sendcode'] == ''){
        $jsonArray['error'] = true;
        $jsonArray['error_message'] = 'Invalid Input';
        $jsonArray['error_code'] = '-1';
        echo json_encode($jsonArray);
        return false;
    }
    
    $creditApp = $this->creditapp_model->getByuuid($data['uuid']);
    
    //if uuid is incorrect
    if(!$creditApp){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Invalid Session, please start from the beginning';
             $jsonArray['error_code'] = '-2';
             echo json_encode($jsonArray);
             return;
    }
    
    if($creditApp->confirmattempt > 6){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Too many attempts';
             $jsonArray['error_code'] = '-10';
             echo json_encode($jsonArray);
             return;
    }
    
    $this->creditapp_model->addAttempt($data['uuid']);
    
    if(verifycode($creditApp->confirmhash,$data['sendcode'])){
      $this->creditapp_model->phoneConfirm($data['uuid']);
      
      //reuse the cust_id if this customer has already passed phone verification
      $already_applied = $this->creditapp_model->checkAlreadyApplied($creditApp->firstname,$creditApp->middlename,$creditApp->lastname,$creditApp->email,$creditApp->mobile);
      
      if(!$already_applied || $already_applied->custid == ''){
          // submit quick app to AR
         $customer['first_name'] = $creditApp->firstname;
         $customer['mid_name'] = $creditApp->middlename;
         $customer['last_name'] = $creditApp->lastname;
         $customer['cell_phone'] = ArFormat($creditApp->mobile);
         $customer['email'] = $creditApp->email;
         $customer['terms_cond'] = 'Y';
         $customer['referal_code'] = $creditApp->refcode;
         $customer['tcpa'] = $creditApp->tcpa;
         $customer['ip_address'] = $creditApp->ip_address;
         
         $arResponse = arcall($customer,'mobileAppMainScreen');
         
           if($arResponse->OK){
             
             // add customer AR id in database
             $this->creditapp_model->quickUpdate($arResponse,$creditApp->id);
             
             $jsonArray['error'] = false;
             $jsonArray['error_message'] = '';
             $jsonArray['error_code'] = '';
             $jsonArray['uuid'] = $data['uuid'];
             $jsonArray['verified'] = true;
             echo json_encode($jsonArray);
             return;
           }else{
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = $arResponse->INFO;
             $jsonArray['error_code'] = '-11';
             echo json_encode($jsonArray);
             return;
           } 
         }else{
            //already has cust_id in DB so bypass ar call
             $cust_id = $already_applied->custid;
             //update cust id in DB instead of making AR webservice call
             $this->creditapp_model->quickUpdateForExist($cust_id,$creditApp->id);
             $jsonArray['error'] = false;
             $jsonArray['error_message'] = '';
             $jsonArray['error_code'] = '';
             $jsonArray['uuid'] = $data['uuid'];
             $jsonArray['verified'] = true;
             echo json_encode($jsonArray);
             return;
           } 
        //fail on verifying phone number  
        }else{
               $jsonArray['error'] = true;
               $jsonArray['error_message'] = 'Invalid Code';
               $jsonArray['error_code'] = '-6';
               $jsonArray['uuid'] = $data['uuid'];
               echo json_encode($jsonArray);
               return;
        }
  }
}