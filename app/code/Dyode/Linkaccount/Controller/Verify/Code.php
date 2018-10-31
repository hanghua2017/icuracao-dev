<?php
/*
Date: 10/07/2018
Author :Kavitha
*/

namespace Dyode\Linkaccount\Controller\Verify;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Dyode\ARWebservice\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;

class Code extends \Magento\Framework\App\Action\Action
{

  /**
   * @var \Magento\Framework\Controller\Result\JsonFactory
   */
    protected $_resultJsonFactory;

    /**
     * @var Magento\Customer\Model\Session $customerSession
     */
    protected $_customerSession;

    /**
     * @var Dyode\ARWebservice\Helper\Data
     */
    protected $_helper;

    /**
     * Constructor
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
       Context $context,
       Session $customerSession,
       JsonFactory $resultJsonFactory,
       Data $helper
    ) {
       parent::__construct($context);
       $this->_resultJsonFactory = $resultJsonFactory;
       $this->_customerSession = $customerSession;
       $this->_helper = $helper;
    }

   public function execute()
   {
      $verifytype = $this->getRequest()->getParam('verifytype', false);
      if ( isset( $verifytype ) ) {

        $customerInfo  = $this->_customerSession->getCuracaoInfo();
        $curacaoCustId = trim($customerInfo->getAccountNumber());
        $accountInfo   =  $this->_helper->getARCustomerInfoAction($curacaoCustId);
        $phone  =  $accountInfo->PHONE;

        $resultData = '';

        /* verifyType 0 -> Send code as text
        *             1-> Send code as Voice
        */
          switch ( $verifytype ){
                case 0 :
                    $resultData = $this->_helper->phoneVerifyCode($phone, 1, 0);
                    break;

                case 1:
                    $resultData = $this->_helper->phoneVerifyCode($phone, 1, 1);
                    break;
          }

          if($resultData != -1){
            $this->_customerSession->setEncCode($resultData);
          }
          $resultJson = $this->_resultJsonFactory->create();

          return $resultJson->setData($resultData);
      }

   }
}
