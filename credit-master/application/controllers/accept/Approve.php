<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approve extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
    
	public function index()
	{
    // $store = $this->preapp_model->isMobile() ? 'mobile' : 'desktop';
    if(is_null($this->session->userdata('decison'))){
      $data['couponcode'] = '';
      $data['couponvalue'] = '';
      $data['fname'] = '';
      $data['accountnumber'] = '';
      $data['creditline'] = '';
      $data['ccv'] = '';
    }
    $this->load->view('preapprove/header',$data);
    $this->load->view('preapprove/approve',$data);
	}
  
}
  
