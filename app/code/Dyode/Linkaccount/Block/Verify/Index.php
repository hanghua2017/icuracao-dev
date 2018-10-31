<?php


namespace Dyode\Linkaccount\Block\Verify;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
    * @var \Magento\Backend\Block\Template\Context
    */
    protected $_storeManager;

    /**
     * @var
     */
    protected $baseUrl;
     /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL for POST request
     *
     * @return string
     */
    public function getFormAction()  {

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();

        return $baseUrl.'linkaccount/verify/index';
    }

    /**
    * Get form action URL for POST request
    *
    * @return string
    */
    public function getCodeFormAction()  {
        
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();

        return $baseUrl.'linkaccount/verify/codeverify';

    }
}
