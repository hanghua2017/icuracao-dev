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
    public function __construct(
          \Magento\Customer\Model\Session $customerSession,
          \Psr\Log\LoggerInterface $logger
     ) {
      $this->_customerSession = $customerSession;
      $this->_logger = $logger;
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
      if ($this->_customerSession->isLoggedIn()) {
         $customerId = $this->_customerSession->getCustomerId();
       }
       $this->_logger->info("customer Id ".$customerId );
       $order = $observer->getEvent()->getOrder();
       return false;
    }
}
