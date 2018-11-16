<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step4 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $this->load->view('credit-app/header');
      $this->load->view('credit-app/step4');
    }else{
        $data = $this->input->post();
        if(empty($data) || $data['uuid'] == '' || $data['bank_type'] == '' || $data['live_year'] == '' || $data['emp_type'] == '') {
                  $jsonArray['error'] = true;
                  $jsonArray['error_message'] = 'Invalid Input';
                  $jsonArray['error_code'] = '-1';
                  echo json_encode($jsonArray);
                  return false;
        }else{
          $creditApp = $this->creditapp_model->getByuuid($data['uuid']);
          
          if(!$creditApp){
                  $jsonArray['error'] = true;
                  $jsonArray['error_message'] = 'Invalid Input';
                  $jsonArray['error_code'] = '-2';
                  echo json_encode($jsonArray);
                  return;
          }
          
          if($creditApp->step < 3 || $creditApp->phoneconfirm != 'Y' || $creditApp->status != 'PENDING'){
                     $jsonArray['error'] = true;
                     $jsonArray['error_message'] = 'Wrong Step';
                     $jsonArray['error_code'] = '-5';
                     echo json_encode($jsonArray);
                     return;
          }
          
          $data['id'] = $creditApp->id;
          
          if($this->creditapp_model->addStep4($data)){
                     $jsonArray['error'] = false;
                     $jsonArray['error_message'] = '';
                     $jsonArray['error_code'] = '';
                     $jsonArray['uuid'] = $data['uuid'];
                     echo json_encode($jsonArray);
                     return;
          }else{
                     $jsonArray['error'] = true;
                     $jsonArray['error_message'] = 'Invalid Input';
                     $jsonArray['error_code'] = '-3';
                     echo json_encode($jsonArray);
                     return;
          }
        }
    }
  }
}