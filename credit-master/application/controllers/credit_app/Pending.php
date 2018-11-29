<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pending extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    $store = $this->creditapp_model->isMobile() == TRUE ? 'mobile' : 'desktop';
    $data['store'] = $store;
    $this->load->view('credit-app/header',$data);
    $this->load->view('credit-app/pending');
  }
}