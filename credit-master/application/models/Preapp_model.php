<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Preapp_model extends CI_Model{
    private $debugging;
    
    
    public function _construct() {
        $this->debugging = true;
        parent::_construct();
    }
    
    //check the device type
    public function isMobile(){
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    
    public function start($promo,$created,$referenceFlag){
       $mobile = $this->isMobile() ? 'mobile' : 'desktop';
       
       $this->load->database();
       
       $data = array(
             "paID"                 => null,
             "promo"                => $promo,
             "created_date"         => $created,
             "addressverification"  => 0,
             "phonefailed"          => 0,
             "ip_address"           => $_SERVER['REMOTE_ADDR'],
             "referal_url"          => $_SERVER['HTTP_REFERER'],
             "referal_full_path"    => $_SERVER['HTTP_REFERER'],
             "device"               => $mobile,
             'browser'              => $_SERVER['HTTP_USER_AGENT'],
             'referenceFlag'        => $referenceFlag
             );
      $this->db->set($data);
      $query = $this->db->insert('preapproved');
      $insert_id = $this->db->insert_id();
      return $insert_id;
    }
    
    public function step1($_prid, $_fname, $_lname, $_street, $_city, $_state, $_zip, $_dob, $_pphone, $_sphone, $_reference, $_tcpa = 0, $_ssn =  null){
      $this->load->database();
      $data = array(
                        'fname'     =>     $_fname,
                        'lname'     =>     $_lname,
                        'street'    =>     $_street,
                        'city'      =>     $_city,
                        'state'     =>     $_state,
                        'zip'       =>     $_zip,
                        'dob'       =>     $_dob,
                        'pphone'    =>     $_pphone,
                        'sphone'    =>     $_sphone,
                        'referenceFlag' => $_reference,
                        'tcpa'      =>     $_tcpa,
                        'ssn'      =>      $_ssn,
                        'addressverification' => 1,
                        'step1' => 1
        );
      $this->db->set($data);
      $this->db->where('paID',$_prid);
      $this->db->update('preapproved');
    }
    
    public function updateverify($_check,$_paid){
      $this->load->database();
      $data = array(
                        'paID'                     =>     $_paid,
      );
      
      if($_check == false){
          $this->db->set('phoneverified',0);
          $this->db->set('phonefailed',1);
      }else{
          $this->db->set('phoneverified',1);
          $this->db->set('phonefailed',0);
      }
      $this->db->where($data);
      $this->db->update('preapproved');
    }
    
    public function checkPhoneVerify($paID){
      $this->load->database();
      $this->db->select('phoneverified');
      $this->db->from('preapproved');
      $this->db->where('paID',$paID);
      $query = $this->db->get();
      $data = $query->result(); 
      if(empty($data)){
        return false;
      }
      $row = $data[0]; 
      if($row->phoneverified == 1){
        return true;
      }else{
        return false;
      }
    }
    
    public function updatestep2a($_paid, $_license, $_income, $_email, $_password, $_ref1fname, $_ref1lname, $_ref1phone, $_ref1relation, $_ref2fname, $_ref2lname, $_ref2phone, $_ref2relation, $_licensestate){
      
      $this->load->database();
      $data = array(
                'step2'        =>     1,
                'state_id'     =>     $_license,
                'income'       =>     $_income,
                'email'        =>     $_email,
                'ref1fname'    =>     $_ref1fname,
                'ref1lname'    =>     $_ref1lname,
                'ref1phone'    =>     $_ref1phone,
                'ref1relation' =>     $_ref1relation,
                'ref2fname'    =>     $_ref2fname,
                'ref2lname'    =>     $_ref2lname,
                'ref2phone'    =>     $_ref2phone,
                'ref2relation' =>     $_ref2relation,
                'licensestate' =>     $_licensestate
      );
      $this->db->where('paID',$_paid);
      $this->db->update('preapproved',$data);
    }
    
    public function step2($_paid, $_tcpa, $_ssn, $_arssn, $_ssn1, $_ssn2, $_ssn3){
      $this->load->database();
      
      $this->db->select('*');
      $this->db->from('preapproved');
      $this->db->where('paID',$_paid);
      $query = $this->db->get();
      $data = $query->result(); 
      
      $row = $data[0];
      if(!isset($row)){
        return false;
      }
      
      $dobdate = new DateTime($row->dob);

        $tcpa = 'N';
        if( $row->tcpa == 1 ){ $tcpa = 'Y'; }
        
        if($_ssn1 != '' && $_ssn2 != '' && $_ssn3 != ''){
          $ssn_ar = $_ssn1.$_ssn2.$_ssn3;
        }else{
          $ssn_ar = $_ssn[0] . $_arssn;
        }
        
        $data = array(
          'LastName' => $row->lname,
          'FirstName' => $row->fname,
          'MiddleInitial' => '',
          'HomePhone' => ArFormat($row->pphone),
          'eMail' => $row->email,
          'Street' => trim($row->street),
          'City' => $row->city,
          'State' => $row->state,
          'Zip' => $row->zip,
          'Phone2' => $row->sphone,
          'SSN' => $ssn_ar,
          'ID' => $row->state_id,
          'IDType' => 'AU1',
          'MotherMaidenName' => '',
          'WorkName' => '',
          'WorkPhone' => '',
          'DOB' => $dobdate->format('Y-m-d\Th:i:s'),
          'IDExpiration' => date('Y-m-d\Th:i:s', mktime(0, 0, 0, '01', '01', (string)(date("Y")+1)  )),
          'LenghtInCurrAddress' => '',
          'LenghtInCurrWork' => '',
          'AnnualIncome' => $row->income,
          'IDState' => '',
          'IPAddress' => $_SERVER['REMOTE_ADDR'],
          'Language' => 'E',
          'AGPP' => 'N',
          'Submit' => 'Y',
          'TCPA' => $tcpa,
          'PromoCode' => $row->promo
          );
          
        $result = soaparcall($data,'WebCustomerApplication')->WebCustomerApplicationResult;
        
        return $result;
    }
    
    public function sendreference($_cust_id, $_fname, $_lname, $_phone, $_relation){
            $_phone = trim($_phone);

            $data = array(
                            'cust_id' => $_cust_id,
                            'f_name' => $_fname,
                            'l_name' => $_lname,
                            'phone' => ArFormat($_phone),
                            'relation' => $_relation
            );
            $result = soaparcall($data,'AddCustomerReference')->AddCustomerReferenceResult;
            
            return $result;  
    }
    
    public function updatestep2b($_paid, $_status, $_accn, $_creditlimit, $_results){
      $this->load->database();
      $data = array(
        'accnumber'               =>     $_accn,
        'approvedcreditlimit'     =>     $_creditlimit,
        'finalmessage'            =>     $_results
      );
      $this->db->where('paID',$_paid);
      switch($_status){
        case 'approve':
          $this->db->set('approve',1);
          break;
        case 'pending':
          $this->db->set('pending',1);
          break;
        case 'decline':
          $this->db->set('decline',1);
          break;
        case 'duplicate':
          $this->db->set('duplicate',1);
          break;
        default:
          $this->db->set('error',1);
          break;
      }
      $this->db->update('preapproved',$data);
    }
    
    public function get_name($_paid){
      $this->load->database();
      $this->db->select('fname');
      $this->db->from('preapproved');
      $this->db->where('paID',$_paid);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      $row = $data[0];
      return $row->fname;
    }
    
    public function sendemail($_paid, $_type, $_ccv){
      $emailtosend = '';
      $this->load->database();
      $this->db->select('promo, email, fname, lname, pphone, accnumber, approvedcreditlimit');
      $this->db->from('preapproved');
      $this->db->where('paID',$_paid);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      $row = $data[0];
      $phone = '1'.$row->pphone;
      $credit = (int)trim($row->approvedcreditlimit);      
      $campaigncode = $row->promo;
      $campaigncode = $campaigncode[0].$campaigncode[1];
      switch($_type){
        case 'APPROVED':
          $emailtosend = tok(lang('preapp_emailapproved'),array('email' => $row->email, 'fname' => $row->fname, 'lname' => $row->lname, 'accn' => $row->accnumber, 'credit' => $credit, 'ccv' => $_ccv, 'campaigncode' => $campaigncode, 'phone' => $phone));
          break;
        case 'DECLINED':
          $emailtosend = tok(lang('preapp_emaildeclined'),array('email' => $row->email, 'fname' => $row->fname, 'lname' => $row->lname, 'campaigncode' => $campaigncode, 'phone' => $phone));
          break;
        case 'PENDING':
          $emailtosend = tok(lang('preapp_emailpending'),array('email' => $row->email, 'fname' => $row->fname, 'lname' => $row->lname, 'accn' => $row->accnumber, 'campaigncode' => $campaigncode, 'phone' => $phone));
          break;
        default:
          break;
      }
      // Send email
      file_get_contents($emailtosend);
      
      $this->db->set('emailsend',1);
      $this->db->where('paID',$_paid);
      $this->db->update('preapproved');
    }
    
    public function removeSessionData(){
      $this->session->set_userdata("step1",null);
      $this->session->set_userdata("step2",null);
      $this->session->set_userdata("step3",null);
    }
    
    public function createPreappCustomer($_paid,$email,$password,$custid){
      $this->load->database();
      $this->db->select('*');
      $this->db->from('preapproved');
      $this->db->where('paID',$_paid);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      $row = $data[0];
      var_dump($row);return;
      $fname = $row->fname;
      $lname = $row->lname;
      $street = $row->street;
      $city = $row->city;
      $state = $row->state;
      $regionId = getRegionId($state);
      $zip = $row->zip;
      $phone = $row->pphone;
      
      $token = adminToken();
      
      $customerData = [
        'customer' => [
            "email" => $email,
            "firstname" => $fname,
            "lastname" => $lname
        ],
        "password" => $password
      ];

    $customerData['customer']['addresses'] = array(
      "0" => array(
        "firstname" => $fname,
        "lastname" => $lname, 
        "street" => array('0' =>$street,'1'=>''),
        "city" => $city,
        "region_id" => $regionId,
        "postcode" => $zip,
        "country_id" => "US",
        "telephone" => $phone,
        "default_shipping" => true,
        "default_billing" => true
      )
    );

    $customerData['customer']['extension_attributes'] = array(
      "is_subscribed" => true
    );

    $customerData['customer']['custom_attributes'][2] = array(
      "attribute_code" => "curacaocustid",
      "value" => $custid
    );
    
    $domain = $_SERVER['SERVER_NAME'];
    $ch = curl_init("http://$domain/rest/V1/customers");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

    $result = curl_exec($ch);

    $result = json_decode($result, 1);
    
    if(isset($result['id'])){
      return true;
    }else{
      return false;
    }
      
  }
}