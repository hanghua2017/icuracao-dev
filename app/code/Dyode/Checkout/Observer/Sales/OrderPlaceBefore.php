<?php
/*
Date: 17/07/2018
Author :Kavitha
*/

namespace Dyode\Checkout\Observer\Sales;

class OrderPlaceBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $_customerSession;
    protected $_logger;
    protected $_helper;

    public function __construct(
          \Magento\Customer\Model\Session $customerSession,
          \Dyode\ARWebservice\Helper\Data $helper,
          \Psr\Log\LoggerInterface $logger
     ) {
      $this->_customerSession = $customerSession;
      $this->_logger = $logger;
      $this->_helper = $helper;
     }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
    /*  if ($this->_customerSession->isLoggedIn()) {
         $customerId = $this->_customerSession->getCustomerId();
       }
       $this->_logger->info("customer Id ".$customerId );

       $accountNumber = $this->_coreSession->getCurAcc();
       //Get Customer Contact information
       $accountInfo   =  $this->_helper->getARCustomerInfoAction($accountNumber);

       if($accountInfo !== false){
           //Get the DownPayment

           $postData = array(
               'CustID' => $accountNumber,
               'Zip' =>'',
               'DOB' => '',
               'SSN' => '',
               'MMaiden' => '',
               'Amount' => 1,
               'CCV' => ''
           );
           $validateDp   =  $this->_helper->verifyPersonalInfm($postData);
          // $validateDp = true;
           if($validateDp == false){
              return false;
           }

           $order = $observer->getEvent()->getOrder();
          // $this->_logger->log(100,print_r($order,true));
       }
       return false;*/
    }
}
