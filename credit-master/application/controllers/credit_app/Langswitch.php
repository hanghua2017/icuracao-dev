<?php
class Langswitch extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }
    
    function switchLanguage($language = ""){
      $language = ($_SERVER['QUERY_STRING'] != 'esp') ? 'english' : "spanish";
      $this->session->set_userdata('site_lang', $language);
      redirect($_SERVER['HTTP_REFERER']);
    }
}