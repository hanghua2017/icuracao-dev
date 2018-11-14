<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step5 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $this->load->view('credit-app/header');
      $this->load->view('credit-app/step5');
    }else{
        $data = $this->input->post();
        if(empty($data) || $data['uuid'] == '' || $data['ref1_fname'] == '' || $data['ref1_lname'] == '' || $data['ref1_phone'] == '' || $data['ref1_relation'] == '' || $data['ref2_fname'] == '' || $data['ref2_lname'] == '' || $data['ref2_phone'] == '' || $data['ref2_relation'] == '') {
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
          if($creditApp->step < 4 || $creditApp->phoneconfirm != 'Y'){
                  $jsonArray['error'] = true;
                  $jsonArray['error_message'] = 'Wrong Step';
                  $jsonArray['error_code'] = '-5';
                  echo json_encode($jsonArray);
                  return;
          }
          
          $data['id'] = $creditApp->id;
          
          //update step to 5 in main table organicapp and update record in table organic_reference
          if(!$this->creditapp_model->addStep5($data)){
                  $jsonArray['error'] = true;
                  $jsonArray['error_message'] = 'Invalid Input';
                  $jsonArray['error_code'] = '-3';
                  echo json_encode($jsonArray);
                  return;
          }
          
          $customer['cust_id']            = $creditApp->custid;
          $customer['bankAccountType']    = $creditApp->banking;
          $customer['employerName']       = $creditApp->company;
          $customer['employerphone']      = ArFormat($creditApp->companyphone);
          $customer['employerindustry']   = $creditApp->employment;
          $customer['timeWorkYears']      = $creditApp->work_year;
          $customer['timeWorkMonths']     = $creditApp->work_month;      
          $customer['timeHomeYears']      = $creditApp->live_year;
          $customer['timeHomeMonths']     = $creditApp->live_month;
          $customer['ref1_relationship']  = $data['ref1_relation'];
          $customer['ref1_f_name']        = $data['ref1_fname'];
          $customer['ref1_l_name']        = $data['ref1_lname'];
          $customer['ref1_phone']         = ArFormat($data['ref1_phone']);
          $customer['ref2_relationship']  = $data['ref2_relation'];
          $customer['ref2_f_name']        = $data['ref2_fname'];
          $customer['ref2_l_name']        = $data['ref2_lname'];
          $customer['ref2_phone']         = ArFormat($data['ref2_phone']);
          
          $arResponse = arcall($customer,'mobileFullApp');
          
          //update record in main table organicapp and ar response in table organic_arlogs
          $this->creditapp_model->fullUpdate($arResponse,$creditApp->id);
          
          if(!$arResponse->OK){
            $jsonArray['error'] = true;
            $jsonArray['error_message'] = $arResponse->INFO;
            $jsonArray['error_code'] = '-11';
            //log the buggy response
            $this->creditapp_model->logARResponse($arResponse, 'mobileFullApp',$creditApp->id);
            echo json_encode($jsonArray);
            return;
          }else{
            $jsonArray['error'] = false;
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
                        
                        $emailUrl = 'http://link.icuracao.com/u/register.php?CID=765674263&f=2107&p=2&a=r&SID=&el=&llid=&counted=&c=&optin=y&'.'inp_3='.$creditApp->email.'&inp_1='.ucfirst($creditApp->firstname).'&inp_2='.ucfirst($creditApp->lastname).'&inp_17446='.$jsonArray['account'].'&inp_17445=Pending&'.'&inp_37=1'.$creditApp->mobile.'&inp_10630=true&inp_7316=Organic-Credit-Application';
                        
                        file_get_contents($emailUrl);
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
}