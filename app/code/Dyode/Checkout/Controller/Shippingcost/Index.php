<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * 
 */

namespace Dyode\Checkout\Controller\Shippingcost;

use Magento\Framework\App\Action\Action;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteRepository; 
use Magento\Quote\Model\Quote;

class Index extends Action {

   /*
    * @var Magento\Quote\Model\Quote\Item
    */
    protected $quoteItem;
    
    /**
    * @var \Magento\Quote\Model\Quote
    */
    protected $shippingQuote;

    /*
    * @var Magento\Model\Checkout\Session
    */
    protected $checkoutSession;

    /*
    * @var Magento\Quote\Model\QuoteRepository
    */
    protected $quoteRepo;

    
    /**
     * Constructor
     *
     * @param Magento\Framework\App\Action\Context  $context
     * @param Magento\Checkout\Model\Session $checkoutSession
     * @param Magento\Quote\Model\QuoteRepository $quoteRepo
     */
    public function __construct(
        Context $context,
        Item $quoteItem,
        Session $checkoutSession,
        QuoteRepository $quoteRepo,
        Quote $shippingQuote
    ){
        parent::__construct($context);
        $this->quoteItem = $quoteItem; 
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepo = $quoteRepo;
        $this->shippingQuote = $shippingQuote;
    }

    /**
     * Execute view action
     * @return \Magento\Framework\Controller\ResultInterface
    */
    public function execute(){
        $quote = $this->checkoutSession->getQuote();
      //  $quote = $this->quoteRepo->getById($quoteInfo->getId());
      
        $newShipArr = array();
        $shippingDetails = array(
            array(
                'shipping_carrier_code'=>'tablerate',
                'shipping_method_code'=>'bestway',
                'quote_itemid'=>399
            ),
            array(
                'shipping_carrier_code'=>'UPS',
                'shipping_method_code'=>'GND',
                'quote_itemid'=>400
            )
        );
        //echo 
        //Traverse the shipping details array
        foreach($shippingDetails as $shipping){
           $quoteItemId = $shipping['quote_itemid'];

           $quoteItem = $quote->getItemById($quoteItemId);

           //If no quoteItem found continue the loop
           if (!$quoteItem) {
               continue;
           }
           $quoteItem->setShippingDetails(json_encode($shipping));
           
        }   
       
        $quote->save();
    }
}