<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CheckAddress extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }

  public function index(){
    $address = $data = $this->input->post();
    
    $creditApp = $this->creditapp_model->getByuuid($data['uuid']);
    
    if(!$creditApp){
             $jsonArray['error'] = true;
             $jsonArray['error_message'] = 'Invalid Input';
             $jsonArray['error_code'] = '-2';
             echo json_encode($jsonArray);
             return;
    }
    
    $addressInfo = $this->creditapp_model->checkAddress($address);
    echo $addressInfo;
  }

}