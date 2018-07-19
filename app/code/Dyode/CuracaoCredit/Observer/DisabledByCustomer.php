<?php
namespace Dyode\CuracaoCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
class DisabledByCustomer implements ObserverInterface
{
    protected $_logger;
    protected $_customerSession;
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession
      )
    {
        $this->_logger = $logger;
        $this->_customerSession = $customerSession;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        $quote           = $observer->getEvent()->getQuote();
        $this->_logger->info($method_instance->getCode());
        /* If Cusomer logged in then the curacao credit payment method will be displayed */

        $loggedIn = $this->isCustomerLoggedIn();
        if (null !== $quote && !$loggedIn) {
            /* Disable All payment gateway  exclude Your payment Gateway*/
            /* curacao payment method code = curacaopayment */
            if ($method_instance->getCode() == 'curacaopayment') {
                $result->setData('is_available', false);
            }
        }
    }
    /*==== Function to check whether the customer is logged in or not ====*/
    protected function isCustomerLoggedIn() {
       return $this->_customerSession->isLoggedIn();
   }

}
