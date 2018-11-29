<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
        
        $site_lang = $ci->session->userdata('site_lang');
        
        //site_lang is null at the init session so render english
        if(isset($site_lang)){
          $ci->lang->load('credit',$ci->session->userdata('site_lang'));
          $ci->lang->load('preapprove',$ci->session->userdata('site_lang'));
        }else{
          $ci->lang->load('credit','english');
          $ci->lang->load('preapprove','english');
          
        }
        
    }
}