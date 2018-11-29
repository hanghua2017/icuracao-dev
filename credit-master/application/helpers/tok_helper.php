<?php

if(!function_exists('tok')){
  function tok($_str, $_vars) {
      foreach($_vars as $name => $var) {
          $_str = str_replace('{' . $name . '}', $var, $_str);
      }
      return $_str;
  }
}