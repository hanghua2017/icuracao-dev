<?php

namespace Dyode\Linkaccount\Block\Verify;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

     /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->_coreSession = $coreSession;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get curacao account number
     *
     * @return string
     */
    public function getCuracaoId() {
        $curacaoId = substr($this->customerSession->getCurAcc(), -4);

        return $curacaoId;
    }

    /**
    * Get curacao account number
    *
    * @return string
    */
    public function getCustomer() {
        $customerName = $this->customerSession->getFname();

        return $customerName;
    }

}
