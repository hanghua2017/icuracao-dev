<?php
namespace Dyode\Linkaccount\Controller\Verify;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Code extends \Magento\Framework\App\Action\Action {

    protected $_resultJsonFactory;
    protected $_coreSession;
    protected $_helper;
    /**
     * Constructor
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\Session\SessionManagerInterface $coreSession,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
       \Dyode\ARWebservice\Helper\Data $helper
    ) {
       parent::__construct($context);
       $this->_resultJsonFactory = $resultJsonFactory;
       $this->_coreSession = $coreSession;
       $this->_helper = $helper;
     }
   public function execute()
   {
      $verifytype = $this->getRequest()->getParam('verifytype', false);
      $custInfo   = $this->_coreSession->getCustomerInfo();
      $phone      = $custInfo['PHONE'];
      $resultData = '';
      /* verifyType 0 -> Send code as text
      *             1-> Send code as Voice
      */
      switch($verifytype){
            case 0 :
                $resultData = $this->_helper->phoneVerifyCode($phone, 1, 0);
                break;

            case 1:
                $resultData = $this->_helper->phoneVerifyCode($phone, 1, 1);
                break;
      }
      if($resultData != -1){
        $this->_coreSession->setEncCode($resultData);
      }
      $resultJson = $this->_resultJsonFactory->create();
      return $resultJson->setData($resultData);

   }
}
