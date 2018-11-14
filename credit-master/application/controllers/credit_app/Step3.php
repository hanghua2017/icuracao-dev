<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step3 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $this->load->view('credit-app/header');
      $this->load->view('credit-app/step3');
    }else{
      //$this->creditapp_model->createCustomer('test@icuracao.com','test2','consumer','Test123!');exit;
      
      $data = $this->input->post();
    
      if(empty($data) || $data['uuid'] == '' || $data['country'] == '' || $data['id_type'] == '' || $data['id_number'] == '' || $data['maiden_name'] == '') {
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
        
        // Check if previous steps are complete 
        if($creditApp->step < 2 || $creditApp->phoneconfirm != 'Y'){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Wrong Step';
             $jsonArray['error_code'] = '-5';
             echo json_encode($jsonArray);
             return;
         }
         
         $data['id'] = $creditApp->id;
          
         //insert or update record in organic_id table
         if(!$this->creditapp_model->addStep3($data)){
            $jsonArray['error'] = true;
            $jsonArray['error_message'] = 'Invalid Input';
            $jsonArray['error_code'] = '-3';
            echo json_encode($jsonArray);
            return;
          }
        
          //prepare data and call express web service 
          $customer['cust_id']        = $creditApp->custid;
          $customer['street']         = $creditApp->address1.' '.$creditApp->address2;
          $customer['city']           = $creditApp->city;
          $customer['state']          = $creditApp->state;
          $customer['zip']            = $creditApp->zip;
          $customer['month_income']   = (string) $creditApp->income;
          $customer['dob']            = $creditApp->bday;
          $customer['ssn']            = $data['ssn'];
          $customer['id_sub_type']    = $data['id_type'];
          $customer['id_state']       = $data['id_state'];
          $customer['id_no']          = $data['id_number'];
          $customer['id_country']     = $data['country'];
          $customer['maiden']         = $data['maiden_name'];
          
          $arResponse = arcall($customer,'mobileAppExpressApp');
          
          //update record on the main table organicapp
          $this->creditapp_model->expressUpdate($arResponse,$creditApp->id);
          
          //log the buggy response in DB
          if(!$arResponse->OK){
              $jsonArray['error'] = true;
              $jsonArray['error_message'] = $arResponse->INFO;
              $this->creditapp_model->logARResponse($arResponse,'mobileAppExpressApp',$creditApp->id);
              //improve duplicate language
              if(strpos(strtoupper($arResponse->INFO),'DUPLICATE') !== false){
                $jsonArray['error_message'] = 'It looks like you already have an account with us. Please to go our store or call 1-800-990-3422. Thanks.';
              }
              $jsonArray['error_code'] = '-11';
              echo json_encode($jsonArray);
              return;
          }
          
          $jsonArray['error']             = false;
          $jsonArray['error_message']     = '';
          $jsonArray['error_code']        = '';
          $jsonArray['uuid']              = $data['uuid'];
          $jsonArray['status']            = $arResponse->INFO;
          $jsonArray['account']           = $arResponse->CUST_ID;
          
          if(isset($arResponse->TOT_LIMIT)) {
                $jsonArray['total_limit']       = $arResponse->TOT_LIMIT;
                $jsonArray['ccv']               = $arResponse->CCV;
          }
          
          if($arResponse->INFO == 'APPROVE'){
                      // Create a magento user account for customer
                      $cpass = rand_string(6);
                      
                      //TODO:add custid to this customer after online activiation done                    
                      $this->creditapp_model->createCustomer($creditApp->email,$creditApp->firstname,$creditApp->lastname,$cpass);
                      
                      // send email to customer
                      $emailUrl = 'http://link.icuracao.com/u/register.php?CID=765674263&f=2105&p=2&a=r&SID=&el=&llid=&counted=&c=&optin=y&'.'inp_3='.$creditApp->email.'&inp_1='.ucfirst($creditApp->firstname).'&inp_2='.ucfirst($creditApp->lastname).'&inp_7314='.$jsonArray['account'].'&inp_17445=Approved&inp_17444='.$cpass.'&'.'inp_7325='.$jsonArray['total_limit'].'&inp_7327='.$jsonArray['ccv'].'&inp_37=1'.$creditApp->mobile.'&inp_10630=true&inp_7316=Organic-Credit-Application';
                      
                      file_get_contents($emailUrl);
          }elseif($arResponse->INFO == 'PENDING'){
                      
                      //no email for pending customers at express stage
                      echo json_encode($jsonArray);
                      return;  
          }elseif($arResponse->INFO == 'DECLINE'){
                      
                      $emailUrl = 'http://link.icuracao.com/u/register.php?CID=765674263&f=2108&p=2&a=r&SID=&el=&llid=&counted=&c=&optin=y'.'&inp_3='.$creditApp->email.'&inp_1='.ucfirst($creditApp->firstname).'&inp_2='.ucfirst($creditApp->lastname).'&inp_17445=Decline&inp_7316=Organic-Credit-Application';
                  
                      file_get_contents($emailUrl);
          }
          echo json_encode($jsonArray);
          return;        
        }
    }
  }
}