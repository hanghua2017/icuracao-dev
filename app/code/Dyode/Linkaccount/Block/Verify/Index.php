<?php


namespace Dyode\Linkaccount\Block\Verify;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
    * @var \Magento\Backend\Block\Template\Context
    */
    protected $_storeManager;
    protected $_customerSession;
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
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
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

    /**
     * Fucntion to return the last four digits of the phone number
     */
    public function getCustomerPhone(){
        $customerInfo  = $this->_customerSession->getCuracaoInfo();
        return substr($customerInfo->getTelephone(),-4);
        
    }
}
