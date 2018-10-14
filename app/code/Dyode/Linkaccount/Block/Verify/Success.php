<?php


namespace Dyode\Linkaccount\Block\Verify;

class Success extends \Magento\Framework\View\Element\Template
{
    protected $_coreSession;
     /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->_coreSession = $coreSession;
        parent::__construct($context, $data);
    }

    /**
     * Get curacao account number
     *
     * @return string
     */
    public function getCuracaoId() {
        $curacaoId = $this->_coreSession->getCurAcc();
        return $curacaoId;
    }

    /**
    * Get curacao account number
    *
    * @return string
    */
    public function getCustomer() {
        $customerName = $this->coreSession->getFname()."  ".$this->coreSession->getLastname();                   
        return $customerName;
    }

}
