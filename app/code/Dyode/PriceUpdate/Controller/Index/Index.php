<?php

namespace Dyode\PriceUpdate\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_priceModel;

    protected $_productCollectionFactory;

    public function __construct(  	
	\Magento\Framework\App\Action\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
	\Dyode\PriceUpdate\Model\PriceUpdate $priceModel
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;
	    $this->_priceModel = $priceModel;
	    parent::__construct($context);
	}

	public function execute(){
    	$this->_priceModel->updatePrice();
        
    }
}