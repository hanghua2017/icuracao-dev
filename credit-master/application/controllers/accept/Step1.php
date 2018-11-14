<link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step1.css" />
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step1 extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
      
      //this get method for switching language purpose
      if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
        $step1 = $this->session->userdata('step1');
        if(isset($step1)){
          $data['fname'] = $step1->result->FirstName;
          $data['lname'] = $step1->result->LastName;
          $data['address'] = $step1->result->Street;
          $data['city'] = $step1->result->City;
          $data['state'] = $step1->result->State;
          $data['zip'] = $step1->result->Zip;
          $data['referenceFlag'] = $step1->result->ReferenceFlag == 'Y' ? 'Y' : 'N';
          
          $data['prid'] = $step1->prid;
          $data['arssn'] = $step1->result->SSN;
          $data['promo'] = $step1->promo;
        }else{
          $data['fname'] = "";
          $data['lname'] = "";
          $data['address'] = "";
          $data['city'] = "";
          $data['state'] = "";
          $data['zip'] = "";
          $data['referenceFlag'] = "";
          
          $data['prid'] = "";
          $data['arssn'] = "";
          $data['promo'] = "";
        }
        
        $data['ssn'] = '';
        $data['prim1'] = '';
        $data['prim2'] = '';
        $data['prim3'] = '';
        $data['sec1'] = '';
        $data['sec2'] = '';
        $data['sec3'] = '';
        
        $data['yyyy'] = '';
        $data['dd'] = '';
        $data['mm'] = '';
        
        $this->load->view('preapprove/header');
        $this->load->view('preapprove/step1',$data);
      }else{
        $data = $this->input->post();
        //data validation 
        if(empty($data) || $data['pcode1'] == '' || $data['pcode2'] == '' || $data['pcode3'] == '') {
                  $this->session->set_flashdata('error_message',lang('preapp_landing_error1'));
                  redirect('/accept/index');
        }else{
          $promo = strtoupper($data['pcode1'].$data['pcode2'].$data['pcode3']);
          $promoExpireDate = array("AZ"=>"2018-04-30","BA"=>"2018-04-30","BB"=>"2018-07-31","BC"=>"2018-07-31");
          //if current date is greater than the promo expire date, that means this promo has already expired
          if(array_key_exists(strtoupper($data['pcode1']),$promoExpireDate) && date('Y-m-d') > $promoExpireDate[strtoupper($data['pcode1'])]){
                $this->session->set_flashdata('error_message',lang('preapp_landing_error2'));
                redirect('/accept/index');
          }else{
            $data = array('PromoID'=>$promo);
            
            //check ar webservice connection is good
            $result = soaparcall($data,'CheckPreAppPromo')->CheckPreAppPromoResult;
            if(!isset($result)){
              $this->session->set_flashdata('error_message',lang('preapp_landing_error2'));
              redirect('/accept/index');
            }
            //returns error message "invalid promo code"
            if($result->ErrorID != 0){
              $this->session->set_flashdata('error_message',lang('preapp_landing_error2'));
              redirect('/accept/index');
            }else{
                $exp_date = $result->ExpireData;
                $todays_date = date('Y-m-d');
                $today = strtotime($todays_date);
                $expiration_date = strtotime($exp_date);
                $referenceFlag = $result->ReferenceFlag;
                
                //check for expiration
                if($expiration_date < $today){
                  $this->session->set_flashdata('error_message',lang('preapp_landing_error3').lang('preapp_error4'));
                }
                
                //insert a new record in DB
                $prid = $this->preapp_model->start($promo,$todays_date,$referenceFlag);
                
                //render layout for customer to verify personal info
                $data['fname'] = $result->FirstName;
                $data['lname'] = $result->LastName;
                $data['address'] = $result->Street;
                $data['city'] = $result->City;
                $data['state'] = $result->State;
                $data['zip'] = $result->Zip;
                $data['referenceFlag'] = $result->ReferenceFlag == 'Y' ? 'Y' : 'N';
                
                $data['ssn'] = '';
                $data['prim1'] = '';
                $data['prim2'] = '';
                $data['prim3'] = '';
                $data['sec1'] = '';
                $data['sec2'] = '';
                $data['sec3'] = '';
                
                $data['yyyy'] = '';
                $data['dd'] = '';
                $data['mm'] = '';
                
                $data['prid'] = $prid;
                $data['arssn'] = $result->SSN;
                $data['promo'] = $promo;
                
                $step1 = new \stdClass();
                $step1->result = $result;
                $step1->prid = $prid;
                $step1->promo = $promo;
                
                $this->session->set_userdata("step1",$step1);
                $this->session->set_userdata("decison",null);
                
                $this->load->view('preapprove/header');
                $this->load->view('preapprove/step1',$data);
                
                //without redirect action, if user refresh the browser, it keeps sending post request 
                //this redirect sets the action to get http method
                redirect('/accept/step1');
                
            }
          }
        }
      }

  }
  
  public function checkaddress(){
    $address = $this->input->post();
    $addressInfo = $this->creditapp_model->checkAddress($address);
    echo $addressInfo;
  }

}