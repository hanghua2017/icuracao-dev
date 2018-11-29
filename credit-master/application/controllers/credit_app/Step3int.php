<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step3int extends CI_Controller {

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
      $this->load->view('credit-app/header');
      $this->load->view('credit-app/step3int');
    }
  }
}