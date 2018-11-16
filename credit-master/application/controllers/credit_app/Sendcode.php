<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sendcode extends CI_Controller {
  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    $data = $this->input->post();
    if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' || empty($data) ||  $data['uuid'] == '' || $data['type'] == ''){
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
    
    if($creditApp->confirmsent > 2){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Too many attempts';
             $jsonArray['error_code'] = '-5';
             echo json_encode($jsonArray);
             return;
    }
    
    //send verify code to customer  
    $hash = phonecode($creditApp->mobile,$data['type']);
    
    if($hash == -1){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Faild to verify phone';
             $jsonArray['error_code'] = '-6';
             echo json_encode($jsonArray);
             return;
    }
    
    //update hash means system has already sent verify code to this customer
    $this->creditapp_model->updateHash($data['uuid'],$hash);
    
    $jsonArray['error'] = false;
    $jsonArray['error_message'] = '';
    $jsonArray['error_code'] = '';
    $jsonArray['uuid'] = $data['uuid'];
    echo json_encode($jsonArray);
  }
}