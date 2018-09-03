<?php

namespace Dyode\PriceUpdate\Controller\Index;

/**
 * Price Update Contoller
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_priceModel;

    public function __construct(  	
	\Magento\Framework\App\Action\Context $context,  
	\Dyode\PriceUpdate\Model\PriceUpdate $priceModel
	) {
	    $this->_priceModel = $priceModel;
	    parent::__construct($context);
	}

	public function execute(){
    	$this->_priceModel->updatePrice();
    }
}