<?php

namespace Dyode\CategorySearch\Block\Index;


class Index extends \Magento\Framework\View\Element\Template {

    public function __construct(\Magento\Catalog\Block\Product\Context $context, 
    	\Magento\Catalog\Helper\Category $categoryHelper,
    	array $data = []) {

        parent::__construct($context, $data);
        $this->_categoryHelper = $categoryHelper;

    }

    public function getStoreCategories()
	{
	   return $this->_categoryHelper->getStoreCategories();
	} 


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}