<?php
/*
Date: 17/07/2018
Author :Kavitha
*/

namespace Dyode\Checkout\Observer\Sales;

class OrderPlaceBefore implements \Magento\Framework\Event\ObserverInterface
{   

    // Constant Codes
    const DELIVERY_OPTION_SHIP_TO_HOME_ID = 1;
    const DELIVERY_OPTION_STORE_PICKUP_ID = 2;
    const DELIVERY_OPTION_SHIP_TO_HOME = "ship_to_home";
    const DELIVERY_OPTION_STORE_PICKUP = "store_pickup";
    
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

       //$quote = $observer->getQuote();
       $order = $observer->getEvent()->getOrder();      
       $quoteId = $order->getQuoteId();
       $quote = $this->quoteFactory->create()->load($quoteId);
       // Find aggregate shipping cost
       $this->aggregateOrderShippingCost($quote);      
       // Save aggregate shipping rates to quote
       $quote->getShippingAddress()->setData('shipping_amount',$this->_shippingCost);
       $quote->save();

    }
    /**
     * Set shipping cost against quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return void
     */
    private function aggregateOrderShippingCost($quote) {
        
       $quoteItems = $quote->getAllItems();
       
       // Check if quoteitems available
       if (!empty($quoteItems)) { 
            /**
            * Iterate through each quote item and get its shipping cost and store it against order
            */
            foreach($quoteItems as $quoteItem) {
                // Required Information 
                $quoteId = $quoteItem->getItemId();
                $deliveryType = $quoteItem->getDeliveryType();
                // Check if delivery type is "Ship to Home" if so get Shipping Cost and aggregate  
                if($deliveryType == self::DELIVERY_OPTION_SHIP_TO_HOME_ID) {
                    $shippingDetails = json_decode($quoteItem->getShippingDetails());
                    $shippingCost = $shippingDetails->amount;
                    // Aggregate shipping cost
                    $this->_shippingCost += $shippingCost;
                }        
            }
        }
        
    }
}
