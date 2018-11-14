<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step2 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $this->load->view('credit-app/header');
      $this->load->view('credit-app/step2');
    }else{
      $data = $this->input->post();
      //data validation 
      if(empty($data) || $data['uuid'] == '' || $data['street1'] == '' || $data['zip'] == '' || $data['city'] == '' || $data['state'] == '' || $data['income'] == '' || $data['dob'] == '') {
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Invalid Input';
                $jsonArray['error_code'] = '-1';
                echo json_encode($jsonArray);
                return false;
      }else{
        if($data['income'] < 400){
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Sorry the minimum montly income we accpet is $400';
                $jsonArray['error_code'] = '-4';
                echo json_encode($jsonArray);
                return;
        }
        
        if(date("Y") - substr($data['dob'],0,4) < 21){
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Sorry the minimum applying age is 21';
                $jsonArray['error_code'] = '-5';
                echo json_encode($jsonArray);
                return;
        }
        
        $creditApp = $this->creditapp_model->getByuuid($data['uuid']);
        
        if(!$creditApp){
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Invalid Input';
                $jsonArray['error_code'] = '-2';
                echo json_encode($jsonArray);
                return;
        }
        
        // Check if previous steps are complete 
        if($creditApp->step < 1){
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Wrong Step';
                $jsonArray['error_code'] = '-5';
                echo json_encode($jsonArray);
                return;
        }
        
        if($this->creditapp_model->addStep2($data)){
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