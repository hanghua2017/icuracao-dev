<?php

namespace Dyode\CategoryBanner\Block\Index;


class Index extends \Magento\Framework\View\Element\Template {

	/**
	 * @param \Magento\Framework\Registry $registry
	 */

	protected $_coreRegistry;

	protected $_storeManager;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, 
    	\Magento\Framework\Registry $registry,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	array $data = []) {
    	$this->_coreRegistry = $registry;
    	$this->_storeManager = $storeManager;
        parent::__construct($context, $data);

    }


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCurrentCategory()
    {
        $category = $this->_coreRegistry->registry('current_category');

        return $category;
    }

    public function getMediaUrl()
    {
	    $mediaUrl = $this->_storeManager
	                     ->getStore()
	                     ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	    return $mediaUrl;
	}

	public function getBaseUrl()
    {
	    $baseUrl = $this->_storeManager
	                     ->getStore()
	                     ->getBaseUrl();
	    return $baseUrl;
	}

}