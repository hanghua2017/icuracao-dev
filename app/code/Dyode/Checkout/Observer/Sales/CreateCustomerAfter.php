<?php


namespace Dyode\Checkout\Observer\Sales;

class CreateCustomerAfter implements \Magento\Framework\Event\ObserverInterface
{ 

    protected $_customerSession;
    protected $_logger;
    protected $_helper;
    protected $_shippingCost = 0;
    /*
    *  \Magento\Quote\Model\QuoteFactory $quoteFactory
    */
    protected $quoteFactory;

    public function __construct(
          \Magento\Customer\Model\Session $customerSession,
          \Dyode\ARWebservice\Helper\Data $helper,
          \Psr\Log\LoggerInterface $logger,
          \Magento\Quote\Model\QuoteFactory $quoteFactory
     ) {
        $this->_customerSession = $customerSession;
        $this->_logger = $logger;
        $this->_helper = $helper;
        $this->quoteFactory = $quoteFactory;
     }

     public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if (!($this->_customerSession->isLoggedIn())) {
            $this->_logger->info("CreateCustomerAfter Id ");
            $order = $observer->getEvent()->getOrder();      
            $quoteId = $order->getQuoteId();
            $this->_logger->info("QuoteId = ".$quoteId);
            $this->_logger->info("Order Id  = ".$order->getId());
        }
    }
}