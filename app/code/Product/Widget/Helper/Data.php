<?php

namespace Product\Widget\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
  public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		array $data = []
	)
	{
		$this->_productRepository = $productRepository;
		parent::__construct($context, $data);
	}
  public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}
       public function Discountprice($sku)
       {
                     $this->_productRepository->get($sku);
                     $_price= $product->getPrice();
                     $_finalPrice= $product->getSpecialPrice();
                     $specialPriceFromDate = $product->getSpecialFromDate();
                     $specialPriceToDate = $product->getSpecialToDate();
                     $today =  time();
                     if ($_finalPrice && ($product->getPrice()>$product->getFinalPrice())):
                       if($today >= strtotime( $specialPriceFromDate) && $today <= strtotime($specialPriceToDate) ||
                       $today >= strtotime( $specialPriceFromDate) && is_null($specialPriceToDate)):
                       //product discount
                       $_savePercent = 100-round(($_finalPrice / $_price) * 100);

                       endif;
                       endif;
                       return $_savePercent;
       }
}
