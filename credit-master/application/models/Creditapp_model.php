<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Creditapp_model extends CI_Model{
    private $debugging;
    
    
    public function _construct() {
        $this->debugging = true;
        parent::_construct();
    }
    
    //check the device type
    public function isMobile(){
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    
    public function uuidExists($uuid){
      $this->load->database();
      $query = $this->db->get_where('organicapp',array('uuid'=>$uuid));
      $data = $query->result(); 
      if(!empty($data)){
        $row = $data[0];
        if($row->id != ''){
          return true;
        }
      }
      return false;
    }
    
    public function addStep1($customer,$uuid,$ipAddress){
      $this->load->database();
      $mobile = ($this->isMobile()) ? 'mobile' : 'desktop';
      $data = array(
            "firstname"    => $customer['f_name'],
            "middlename"   => $customer['m_name'],
            "lastname"     => $customer['l_name'],
            "mobile"       => $customer['phone'],
            "email"        => $customer['email'],
            "refcode"      => $customer['ref_code'],
            "tcpa"         => $customer['tcpa'],
            "uuid"         => $uuid,
            "ip_address"   => $ipAddress,
            'device'       => $mobile,
            'browser'      => $_SERVER['HTTP_USER_AGENT'],
            'step'         => 1
            );
      $this->db->set($data);
      $query = $this->db->insert('organicapp');
      $insert_id = $this->db->insert_id();
      return $insert_id;
    }
    
    public function getByuuid($uuid){
      $this->load->database();
      
      //update another two tables 
      $this->db->select('*');
      $this->db->from('organicapp as app');
      $this->db->join('organic_id as id', 'app.id = id.app_id','left');
      $this->db->join('organic_reference as ref','app.id = ref.app_id','left');
      $this->db->join('organic_work as work','app.id = work.app_id','left');
      $this->db->where('app.uuid', $uuid);
      
      $query = $this->db->get();
      $data = $query->result(); 
      if(empty($data)){
        return false;
      }
      $row = $data[0]; 
      if($row->id !=  ''){
        return $row;
      }else{
        return false;
      }
    }
    
    public function updateHash($uuid,$hash){
      $this->load->database();
      $this->db->set('confirmhash',$hash);
      $this->db->set('confirmsent','confirmsent+1',FALSE);
      $this->db->where('uuid',$uuid);
      $this->db->update('organicapp');
    }
    
    public function addAttempt($uuid){
      $this->load->database();
      $this->db->set('confirmattempt','confirmattempt+1',FALSE);
      $this->db->where('uuid',$uuid);
      $this->db->update('organicapp');
    }
    
    public function phoneConfirm($uuid){
      $this->load->database();
      $this->db->set('phoneconfirm','Y');
      $this->db->where('uuid',$uuid);
      $this->db->update('organicapp');
    }
    
    public function checkAlreadyApplied($fname,$mname,$lname,$email,$phone){
      $this->load->database();
      $data = array(
        "firstname"  => $fname,
        "middlename" => $mname,
        "lastname"   => $lname,
        "email"      => $email,
        "mobile"     => $phone
      );
      $this->db->select('*');
      $this->db->from('organicapp');
      $this->db->where($data);
      $this->db->order_by("custid", "desc");
      $this->db->limit(1);
      $query = $this->db->get(); 
      $data = $query->result();
      if(!empty($data)){
        return $data[0];
      }
      return false;
    }
    
    public function quickUpdate($arResponse,$app_id){
      $this->load->database();
      $data = array(
        "custid"  => $arResponse->CUST_ID
      );
      
      $this->db->set($data);
      $this->db->where('id',$app_id);
      $this->db->update('organicapp');
      
      $data = array(
        "app_id" => $app_id,
        "webservice" => 'mobileAppMainScreen',
        'response' => json_encode($arResponse, JSON_FORCE_OBJECT)
      );
      
      $this->db->set($data);
      $this->db->insert('organic_arlogs');
      
    }
    
    public function quickUpdateForExist($cust_id,$app_id){
      $this->load->database();
      $data = array(
        "custid"  => $cust_id
      );
      
      $this->db->set($data);
      $this->db->where('id',$app_id);
      $this->db->update('organicapp');
    }
    
    public function checkAddress($data){
      
      if($data['city'] == '' || $data['zip'] == '' || $data['state'] == '' || $data['street'] == ''){
            return json_encode(array("error"=>true,"error_message"=> lang('incomplete_address')));
      }
    
      $bind = array(
        "zip"  => $data['zip'],
        "city" => $data['city'],
        "abbr" => $data['state']
      );
      
      $geo = $this->load->database('geo', TRUE); 
      
      $geo->select('*');
      $geo->from('locations');
      $geo->where($bind);
      
      $query = $geo->get(); 
      $res = $query->result();
      if(empty($res)){
        return json_encode(array("error"=>true,"error_message"=> lang('incorrect_zip')));
      }else{
        //call webservice to validate address
        $addressVal = soaparcall(array("Street" => $data['street'], "Zip" => $data['zip']),'ValidateAddress');
        if(!$addressVal->ValidateAddressResult){
            return json_encode(array("error"=>true,"error_message"=> lang('incorrect_address')));
        }
        return json_encode(array("error"=>false,"error_message"=>''));
      }
    }
    
    public function addStep2($customer){
      $this->load->database();
      $bind = array(
        "uuid"     => $customer['uuid'],
        "address1" => $customer['street1'],
        "address2" => $customer['street2'],
        "city"     => $customer['city'],
        "state"    => $customer['state'],
        "zip"      => $customer['zip'],
        "income"   => $customer['income'],
        "bday"     => $customer['dob'],
        "step"     => 2
      );

      $this->db->set($bind);
      $this->db->set("updated",'NOW()',FALSE);
      $this->db->where('uuid',$customer['uuid']);
      $this->db->update('organicapp');
      return true;
    }
    
    public function addStep3($customer){
      $this->load->database();
      
      $this->db->set("step",3);
      $this->db->set("updated",'NOW()',FALSE);
      $this->db->where("id",$customer['id']);
      $this->db->update("organicapp");
      
      $bind = array(
            "app_id"   => $customer['id'],
            "ssn"      => $customer['ssn'],
            "idtype"   => $customer['id_type'],
            "idstate"  => $customer['id_state'],
            "idnumber" => $customer['id_number'],
            "country"  => $customer['country'],
            "mmname"   => $customer['maiden_name']
      );
      
      $this->db->select("*");
      $this->db->from("organic_id");
      $this->db->where("app_id",$customer['id']);
      $this->db->limit(1);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      //insert record if not exist
      if(empty($data)){
        $this->db->set($bind);
        $this->db->insert("organic_id");
      }else{
        $this->db->where("app_id",$customer["id"]);
        $this->db->update("organic_id",$bind);
      }
      return true;
    }
    
    public function logARResponse($arResponse,$ws_name,$app_id){
      $this->load->database();
      $bind = array(
        "app_id"     => $app_id,
        "webservice" => $ws_name,
        "response"   => json_encode($arResponse,JSON_FORCE_OBJECT)
      );
      
      $this->db->set($bind);
      $this->db->insert("organic_arlogs");
    }
    
    public function expressUpdate($arResponse,$app_id){
      $this->load->database();
      
      if(isset($arResponse->TOT_LIMIT)){
        $bind = array(
          "id"                 => $app_id,
          "custid"             => $arResponse->CUST_ID,
          "status"             => $arResponse->INFO,
          "approvedcreditlimit" => $arResponse->TOT_LIMIT
        );
      }else{
        $bind = array(
          "id"                 => $app_id,
          "custid"             => $arResponse->CUST_ID,
          "status"             => $arResponse->INFO,
          "approvedcreditlimit" => ""
        );
      }
      
      $this->db->where("id",$app_id);
      $this->db->update("organicapp",$bind);
      
      //record the webservice result
      $this->logARResponse($arResponse,'mobileAppExpressApp',$app_id);
    }
    
    public function addStep4($customer){
      $this->load->database();
      
      //update main table organicapp
      $bind = array(
        "id"           => $customer['id'],
        "banking"      => $customer['bank_type'],
        "live_year"    => $customer['live_year'],
        "live_month"   => $customer['live_month'],
        "step"         => 4
      );
      
      $this->db->where("id",$customer['id']);
      $this->db->set("updated",'NOW()',FALSE);
      $this->db->update("organicapp",$bind);
      
      //insert or update record on table organic_work
      $bind = array(
            "app_id"       => $customer['id'],
            "employment"   => $customer['emp_type'],
            "company"      => $customer['company_name'],
            "work_year"    => $customer['work_year'],
            "work_month"   => $customer['work_month'],
            "companyphone" => $customer['company_phone']
      );
      
      $this->db->select("*");
      $this->db->from("organic_work");
      $this->db->where("app_id",$customer['id']);
      $this->db->limit(1);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      //insert record if not exist
      if(empty($data)){
        $this->db->set($bind);
        $this->db->insert("organic_work");
      }else{
        $this->db->where("app_id",$customer["id"]);
        $this->db->update("organic_work",$bind);
      }
      return true;
    }
    
    public function addStep5($customer){
      $this->load->database();
      
      $this->db->set('step',5);
      $this->db->where('id',$customer['id']);
      $this->db->update('organicapp');
      
      //update or insert record in organic_reference table
      $bind = array(
            "app_id"           => $customer['id'],
            "ref1_fname"       => $customer['ref1_fname'],
            "ref1_lname"       => $customer['ref1_lname'],
            "ref1_phone"       => $customer['ref1_phone'],
            "ref1_relation"    => $customer['ref1_relation'],
            "ref2_fname"       => $customer['ref2_fname'],
            "ref2_lname"       => $customer['ref2_lname'],
            "ref2_phone"       => $customer['ref2_phone'],
            "ref2_relation"    => $customer['ref2_relation']
      );
      
      $this->db->select("*");
      $this->db->from("organic_reference");
      $this->db->where("app_id",$customer['id']);
      $this->db->limit(1);
      
      $query = $this->db->get();
      $data = $query->result(); 
      
      //insert record if not exist
      if(empty($data)){
        $this->db->set($bind);
        $this->db->insert("organic_reference");
      }else{
        $this->db->where("app_id",$customer["id"]);
        $this->db->update("organic_reference",$bind);
      }
      
      return true;  
    }
    
    public function fullUpdate($arResponse,$app_id){
      $this->load->database();
      if(isset($arResponse->TOT_LIMIT)){
          $bind = array(
              "id"          => $app_id,
              "custid"          => $arResponse->CUST_ID,
              "status"          => $arResponse->INFO,
              "approvedcreditlimit"=> $arResponse->TOT_LIMIT
                  );
      }else{
          $bind = array(
              "id"          => $app_id,
              "custid"          => $arResponse->CUST_ID,
              "status"          => $arResponse->INFO,
              "approvedcreditlimit"=> ""
                  );
      }
      
      $this->db->where("id",$app_id);
      $this->db->set($bind);
      $this->db->update("organicapp");
      
      $this->logARResponse($arResponse,'mobileFullApp',$app_id);
      return true;
    }
    
    public function checkEmailAvailable($email){
      $api = "rest/V1/customers/isEmailAvailable";
        
      $data = [
        "customerEmail" => $email,
        "websiteId" => 1
      ];
        
      $domain = $_SERVER['SERVER_NAME'];
        
      $ch = curl_init("http://$domain/".$api);

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
      ));

      $result = curl_exec($ch);
      $result = json_decode($result, 1);
      return $result;
    }
    
    
    public function createCustomer($email,$fname,$lname,$password){
      
      $token = adminToken();

      $customerData = [
          'customer' => [
            "email" => $email,
            "firstname" => $fname,
            "lastname" => $lname
            ],
            "password" => $password
      ];

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
        return $result['message'];
      }
    }
    
    public function createCustomerWithCredit($email,$fname,$lname,$password,$custid){
      $token = adminToken();
    
      $customerData = [
        "curacaoid" => $custid,
        "email" => $email,
        "fname" => $fname,
        "lname" => $lname,
        "pass" => $password
      ];
      
      $ch = curl_init("http://$domain/rest/V1/linkaccount/create");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      $result = curl_exec($ch);

      $result = json_decode(json_decode($result));
      return $result->OK;
    }
    
}