<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pending extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
    
	public function index()
	{
    $data['preapp_phone'] = lang('preapp_pending_phone');
    $this->load->view('credit-app/header');
    $this->load->view('credit-app/pending',$data);
	}
  
}
  
