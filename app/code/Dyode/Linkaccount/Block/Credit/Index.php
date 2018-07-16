<?php


namespace Dyode\Linkaccount\Block\Credit;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;
     /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * Get form action URL for POST
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/linkaccount/credit/index';

    }

    /**
    * Returns the Customer ID
    */
    public function getCustomerId(){
      return $this->customerSession->getCustomer()->getId();
    }
}
