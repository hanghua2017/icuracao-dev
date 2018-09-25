<?php

namespace Dyode\CategoryNameWidget\Model\Config\Source;



class Classname implements \Magento\Framework\Option\ArrayInterface

{

   public function toOptionArray()

   {

       return [

       [‘value’ => ‘’, ‘category-widget’ => __(‘Category-widget’)],

       [‘value’ => ‘brand-widget’, ‘label’ => __(‘Brand-widget’)]];

   }

}
