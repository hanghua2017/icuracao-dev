<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step1 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
    
	public function index(){
    //render templates for get request 
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $store = $this->creditapp_model->isMobile() == TRUE ? 'mobile' : 'desktop';
      $data['store'] = $store;
      $this->load->view('credit-app/header',$data);
      $this->load->view('credit-app/step1');
    }else{
      $data = $this->input->post();
      //data validation 
      if(empty($data) || $data['f_name'] == '' || $data['l_name'] == '' || $data['phone'] == '' || $data['tcpa'] == '' || $data['email'] == '' || $data['terms'] != 'Y' || ($data['ref_code'] != '' 
      && !ctype_alnum($data['ref_code']))) {
                $jsonArray['error'] = true;
                $jsonArray['error_message'] = 'Invalid Input';
                $jsonArray['error_code'] = '-1';
                echo json_encode($jsonArray);
            
                return false;
      }else{
        $email = $data['email']; 
        
        //TODO: call magento2 api to check whether the customer exists or not, loaded by email
        if(!$this->creditapp_model->checkEmailAvailable($email)){
                    $jsonArray['error'] = true;
                    $jsonArray['error_message'] = lang('oca_already_exist_email');
                    $jsonArray['error_code'] = '-2';
                    echo json_encode($jsonArray);
                    return false;
        }else{
          //continue applying 
          
          //generate uuid for this customer
          $uuid = gen_uuid();
          //avoid generate duplicate uuid 
          while($this->creditapp_model->uuidExists($uuid)){
              $uuid = gen_uuid();
          }
          
          $recordId = $this->creditapp_model->addStep1($data,$uuid,$_SERVER['REMOTE_ADDR']);
          
          if($recordId == ''){
                  $jsonArray['error'] = true;
                  $jsonArray['error_message'] = 'service is unavailable, try again later';
                  $jsonArray['error_code'] = '-3';
                  echo json_encode($jsonArray);
                  return;
          }else{
                  $jsonArray['error'] = false;
                  $jsonArray['error_message'] = '';
                  $jsonArray['error_code'] = '';
                  $jsonArray['uuid'] = $uuid;
                  echo json_encode($jsonArray);
                  return;
          }
        }
      }
    }
	}
  
  public function checkEmail(){
    $data = $this->input->post();
    echo emailValidate($data['email']);
  }
  
}
