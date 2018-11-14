<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step3 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $step3 = $this->session->userdata('step3');
      if(isset($step3)){
        $data = $step3->data;
      }else{
        $data['prid'] = "";
        $data['ssn'] = "";
        $data['arssn'] = "";
        $data['referenceFlag'] = "Y";
      }
      $data['error'] = $this->session->flashdata('error_message');
      $this->load->view('preapprove/header');
      $this->load->view('preapprove/step3',$data);  
    }else{
        $data = $this->input->post();
        if(empty($data) || $data['prid'] == ''){
          redirect('/accept/index');
        }
        if(!$this->preapp_model->checkPhoneVerify($data['prid'])){
          redirect('/accept/index');
        }else{
          
          $step3 = new \stdClass();
          $step3->data = $data;
          $this->session->set_userdata("step3",$step3);   
          
          $this->load->view('preapprove/header');
          $this->load->view('preapprove/step3',$data);
          redirect('/accept/step3');  
        }
    }
  }
  
  public function checkEmail(){
    $data = $this->input->post();
    if(emailValidate($data['email']) != '1'){
      $jsonArray['error'] = true;
      $jsonArray['error_message'] = 'Invalid Email Address';
      $jsonArray['error_code'] = '-2';
      echo json_encode($jsonArray);
    }elseif(!$this->creditapp_model->checkEmailAvailable($data['email'])){
      $jsonArray['error'] = true;
      $jsonArray['error_message'] = 'Email address already exists with another user!';
      $jsonArray['error_code'] = '-1';
      echo json_encode($jsonArray);
    }else{
      $jsonArray['error'] = false;
      $jsonArray['error_message'] = '';
      echo json_encode($jsonArray);
    }
  }
}