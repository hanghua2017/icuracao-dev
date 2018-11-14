<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step2 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $step2 = $this->session->userdata('step2');
      if(isset($step2)){
        $data = $step2->data;
      }else{
        $data['phonenumber'] = "";
        $data['fname'] = "";
        $data['ssn'] = "";
        $data['arssn'] = "";
        $data['referenceFlag'] = "";
        $data['prid'] = "";
      }
      $this->load->view('preapprove/header');
      $this->load->view('preapprove/step2',$data);  
    }else{
      $data = $this->input->post();
      if(empty($data) || $data['prid'] == '' || $data['fname'] == '' || $data['lname'] == '' || $data['address'] == '' || $data['city'] =='' || $data['state'] =='' ||
       $data['zip'] == '' || $data['yyyy'] == '' || $data['mm'] == '' || $data['dd'] == '' || $data['prim1'] == '' || $data['prim2'] == '' || $data['prim3'] == '') {
                redirect('/accept/index');
      }
      $data['tcpa'] = isset($data['tcpa']) ? 1 : 0;
      $this->preapp_model->step1($data['prid'], 
                                 $data['fname'], 
                                 $data['lname'], 
                                 $data['address'], 
                                 $data['city'], 
                                 $data['state'], 
                                 $data['zip'], 
                                 $data['yyyy'] . '-' . $data['mm'] . '-' . $data['dd'], 
                                 $data['prim1'] . $data['prim2'] . $data['prim3'], 
                                 $data['sec1'] . $data['sec2'] . $data['sec3'],
                                 $data['referenceFlag'],
                                 $data['tcpa'],
                                 $data['ssn']);
      $data['phonenumber'] = $data['prim1'] . '-' . $data['prim2'] . '-' . $data['prim3'];   
      
      $step2 = new \stdClass();
      $step2->data = $data;
      $this->session->set_userdata("step2",$step2);   
                    
      $this->load->view('preapprove/header');
      $this->load->view('preapprove/step2',$data);                         
      
    }
  }
  
  public function sendcode() {
          
          $data = $this->input->post();
          
          switch($data['command']){
              case 'phonecall' :
                  echo phonecode($data['phone'],'call');
                  break;
              
              case 'sendtext':
                  echo phonecode($data['phone'],'text');
                  break;
          }
  }
  
  public function verifycode(){
    $data = $this->input->post();
    $check = verifycode($data['enc'], $data['vid']);
    $this->preapp_model->updateverify($check,$data['prid']);
    if($check){
      echo 1;
    }else{
      echo -1;
    }
  }
}