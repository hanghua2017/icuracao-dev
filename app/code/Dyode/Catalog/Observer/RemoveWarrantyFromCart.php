<?php
/**
 * Created by PhpStorm.
 * User: rajeevktomy
 * Date: 27/09/18
 * Time: 3:49 PM
 */

namespace Dyode\Catalog\Observer;


use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class RemoveWarrantyFromCart implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Catalog\Model\Product
    */
    protected $product;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * RemoveWarrantyFromCart constructor.
     *
     * @param \Magento\Checkout\Model\Cart    $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(CustomerCart $cart, Session $checkoutSession)
    {
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getQuoteItem();
        $quote = $quoteItem->getQuote();
         /*Get all Quote items in cart*/
        $allItems = $quote->getAllVisibleItems();       
        $parentId = $quoteItem->getId();
        $warrantyIds = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $logger->info("inside ++++". $quoteItem->getId());
           
        foreach ($allItems as $eachItem) {
            $logger->info("inside ". $eachItem->getWarrantyParentItemId());
            $warrantyParentId = $eachItem->getWarrantyParentItemId();
            $warrantyId = $eachItem->getId();
            if($parentId == $warrantyParentId){
                $warrantyIds[] = $warrantyId;
                $logger->info("inside if".json_encode($this->checkoutSession->getLockedWarrantyIds()));
                //$this->cart->removeItem($warrantyId)->save();
                //remove warranty id from the checkout session locked variable
                if(is_array($this->checkoutSession->getLockedWarrantyIds())) {
                        $lockedWarrantyIds = $this->checkoutSession->getLockedWarrantyIds();
                        unset($lockedWarrantyIds[$quoteItem->getWarrantyParentItemId()]);
                        $this->checkoutSession->setLockedWarrantyIds($lockedWarrantyIds);
                }
            }
        }
         
        $logger->info("RemoveWarrantyFromCart warrantyIds".json_encode($warrantyIds));            
              
        //Remove all warranty Ids related to that product
        foreach($warrantyIds as $index => $warrantyId){
            $this->cart->removeItem($warrantyId);
        }

        $this->cart->save();


        // if ($quoteItem->getWarrantyParentItemId()) {
        //     $this->cart->removeItem($quoteItem->getWarrantyParentItemId())->save();
           
            

        //     //remove warranty id from the checkout session locked variable
        //     if(is_array($this->checkoutSession->getLockedWarrantyIds())) {
        //         $lockedWarrantyIds = $this->checkoutSession->getLockedWarrantyIds();
        //         unset($lockedWarrantyIds[$quoteItem->getWarrantyParentItemId()]);

        //         $this->checkoutSession->setLockedWarrantyIds($lockedWarrantyIds);
        //     }
        // }

        return $this;
    }
}