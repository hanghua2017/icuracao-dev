<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step4 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
  
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      redirect('/accept/index');
    }else{
      $data = $this->input->post();
      //for referenceFlag as Y
      if(isset($data['ref1prim1']) && isset($data['ref1prim2']) && isset($data['ref1prim3'])){
        $data['ref1phone'] = $data['ref1prim1'] . $data['ref1prim2'] . $data['ref1prim3']; 
      }
      if(isset($data['ref2prim1']) && isset($data['ref2prim2']) && isset($data['ref2prim3'])){
        $data['ref2phone'] = $data['ref2prim1'] . $data['ref2prim2'] . $data['ref2prim3']; 
      }
      if($data['license'] == '' || $data['licensestate'] == '' || $data['email'] == '' || $data['password'] == ''){
        redirect('/accept/index');
      }
      if($data['referenceFlag'] == 'Y'){
        $this->preapp_model->updatestep2a($data['prid'], $data['license'], $data['income'], $data['email'], 
                                          $data['password'], $data['ref1fname'], $data['ref1lname'], $data['ref1phone'], 
                                          $data['ref1relation'], $data['ref2fname'], $data['ref2lname'], $data['ref2phone'], 
                                          $data['ref2relation'], $data['licensestate']);
      }else{
        $this->preapp_model->updatestep2a($data['prid'], $data['license'], $data['income'], $data['email'], 
                                          $data['password'], '', '', '', '', '', '', '', '', $data['licensestate']);
      }
      
      $results = $this->preapp_model->step2($data['prid'], 'N', $data['ssn'], $data['arssn'],$data['ssn1'],$data['ssn2'],$data['ssn3']);
      
      if($results == false){
          redirect('/accept/index');
      }
                                              
      $final = explode('|', $results);  
      //before sending referen if decison is duplicate then the cust id is null, by pass sendreference webservice
      if($final[0] == 'DUPLICATE'){
        $this->preapp_model->updatestep2b($data['prid'], 'duplicate', $final[1], $final[2], $results);
        $preapp_phone =  lang('preapp_duplicate_phone');
        $data['preapp_phone'] = $preapp_phone;
        $this->load->view('credit-app/header');
        $this->load->view('credit-app/duplicate',$data);
        $this->preapp_model->removeSessionData();
        return;
      }
      
      if($data['referenceFlag'] == 'Y') {
        // Send reference 1 to AR
        $this->preapp_model->sendreference($final[1], $data['ref1fname'], $data['ref1lname'], $data['ref1phone'], $data['ref1relation']);
            
        //Send reference 2 to AR
        $this->preapp_model->sendreference($final[1], $data['ref2fname'], $data['ref2lname'], $data['ref2phone'], $data['ref2relation']);
      } 
      
      switch($final[0]){
        
        case 'APPROVED' : 
              // Create customer
              $createdCustomer = $this->preapp_model->createPreappCustomer($data['prid'],$data['email'],$data['password'],$final[1]);
              if($createdCustomer){
                
                //update step2 info
                $this->preapp_model->updatestep2b($data['prid'], 'approve', $final[1], $final[2], $results);
                
                $fname = $this->preapp_model->get_name($data['prid']);
                
                $this->preapp_model->sendemail($data['prid'], 'APPROVED', $final[3]);

                //here is the updated coupon code for November 2017
                $couponcode = '01722417';
                $couponvalue = '$50';
                
                $data['couponcode'] = $couponcode;
                $data['couponvalue'] = $couponvalue;
                $data['fname'] = $fname;
                $data['accountnumber'] = $final[1];
                $data['creditline'] = '$' . money_format('%.2n', $final[2]);
                $data['ccv'] = $final[3];
                $this->load->view('preapprove/header');
                $this->load->view('preapprove/approve',$data);     
                $this->preapp_model->removeSessionData();
                
                $this->session->set_userdata('decison','approved');
                //update HTTP method to GET to prevent duplicate submit
                redirect('/accept/approve');
                
                break;
              }else{
                  $this->session->set_flashdata('error_message',$createdCustomer['message']);
                  redirect('/accept/step3');       
                  break;
              }
              
        case 'PENDING':
              $this->preapp_model->updatestep2b($data['prid'], 'pending', $final[1], $final[2], $results);
              $this->preapp_model->sendemail($data['prid'], 'PENDING', null);
              //share the same pending and decline page with organic credit app, but update the customer service phone number
              $preapp_phone =  lang('preapp_pending_phone');
              $data['preapp_phone'] = $preapp_phone;
              $this->load->view('credit-app/header');
              $this->load->view('credit-app/pending',$data);
              $this->preapp_model->removeSessionData();
              $this->session->set_userdata('decison','pending');
              redirect('/accept/pending');
              break;
        case  'DUPLICATE':
              $this->preapp_model->updatestep2b($data['prid'], 'duplicate', $final[1], $final[2], $results);
              $preapp_phone =  lang('preapp_duplicate_phone');
              $data['preapp_phone'] = $preapp_phone;
              $this->load->view('credit-app/header');
              $this->load->view('credit-app/duplicate',$data);
              $this->preapp_model->removeSessionData();
              $this->session->set_userdata('decison','duplicate');
              redirect('/accept/duplicate');
              break;
        case 'DECLINED':
              $this->preapp_model->updatestep2b($data['prid'], 'decline', $final[1], $final[2], $results);
              $this->preapp_model->sendemail($data['prid'], 'DECLINED', null);
              $preapp_phone =  lang('preapp_pending_phone');
              $data['preapp_phone'] = $preapp_phone;
              $this->load->view('credit-app/header');
              $this->load->view('credit-app/pending',$data);
              $this->preapp_model->removeSessionData();
              $this->session->set_userdata('decison','decline');
              redirect('/accept/pending');
              break;
        default: 
              $this->preapp_model->updatestep2b($data['prid'], 'error', $final[1], $final[2], $results);
              $preapp_phone =  lang('preapp_pending_phone');
              $data['preapp_phone'] = $preapp_phone;
              $this->load->view('credit-app/header');
              $this->load->view('credit-app/pending',$data);
              $this->preapp_model->removeSessionData();
              break;
      }
    }                   
  }
}