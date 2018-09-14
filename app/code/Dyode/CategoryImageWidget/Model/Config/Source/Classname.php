<?php

namespace Dyode\CategoryImageWidget\Model\Config\Source;
/**
 * Class <Classname>
 * @category Dyode
 * @package  Dyode_CategoryImageWidget
 * @module   CategoryImageWidget
 * @author  Nismath V I
 */


class Classname implements \Magento\Framework\Option\ArrayInterface

{

   public function toOptionArray()

   {

       return [

       [‘value’ => ‘’, ‘category-widget’ => __(‘Category-widget’)],

       [‘value’ => ‘brand-widget’, ‘label’ => __(‘Brand-widget’)]];

   }

}
