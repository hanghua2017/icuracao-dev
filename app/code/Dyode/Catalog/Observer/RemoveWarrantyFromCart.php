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
use Magento\Framework\Message\ManagerInterface;
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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * RemoveWarrantyFromCart constructor.
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        CustomerCart $cart, 
        Session $checkoutSession,
        ManagerInterface $messageManager
        )
    {
        $this->cart = $cart;
        $this->messageManager = $messageManager;
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

        foreach ($allItems as $eachItem) {
            $warrantyParentId = $eachItem->getWarrantyParentItemId();
            $warrantyId = $eachItem->getId();
            if ($parentId == $warrantyParentId) {
                $warrantyIds[] = $warrantyId;

                //remove warranty id from the checkout session locked variable
                if (is_array($this->checkoutSession->getLockedWarrantyIds())) {
                    $lockedWarrantyIds = $this->checkoutSession->getLockedWarrantyIds();
                    unset($lockedWarrantyIds[$quoteItem->getWarrantyParentItemId()]);
                    $this->checkoutSession->setLockedWarrantyIds($lockedWarrantyIds);
                }
            }
        }

        //Remove all warranty Ids related to that product
        foreach ($warrantyIds as $index => $warrantyId) {
            $this->cart->removeItem($warrantyId);
        }
        try{
            $this->cart->save();
            $message = __('Successfully deleted');
            $this->messageManager->addSuccessMessage($message);
        } catch(Exception $e){
            $this->messageManager->addErrorMessage(
                'Could not delete the item'
            );
        }
        

        return $this;
    }
}