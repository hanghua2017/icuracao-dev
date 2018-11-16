<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
    
	public function index()
	{
    $store = $this->preapp_model->isMobile() ? 'mobile' : 'desktop';
    $data['store'] = $store;
    $this->load->view('preapprove/header',$data);
    $data['error'] = $this->session->flashdata('error_message');
    $this->load->view('preapprove/landing',$data);
	}
  
}
