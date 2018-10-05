<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;

class UpdateWarrantyInCart implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var array [parent_quote_item_id => warranty_quote_item_id]
     */
    protected $warrantyRelation = [];

    /**
     * @var array
     */
    protected $itemsToUpdate = [];

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * LockWarrantyUpdateInCart constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add warranty to the cart if the warranty is selected.
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $this->cart = $observer->getCart();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $logger->info("hasWarrantyQuoteItem ++++". json_encode($observer->getProduct()));

        if ($this->hasWarrantyQuoteItem() && $this->needToUpdateWarranty()) {
            $this->updateWarrantyQuoteItems();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWarrantyQuoteItem()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $quoteItems = $this->cart->getQuote()->getItems();
        $logger->info("needToUpdateWarranty ++++". json_encode($quoteItems)." count =". count($quoteItems));

        foreach ($this->cart->getItems() as $cartItem) {

            $logger->info("hasWarrantyQuoteItem ++++". json_encode($cartItem->getId()));
            if ($cartItem->getWarrantyParentItemId()) {
                $this->warrantyRelation[(int)$cartItem->getItemId()] = (int)$cartItem->getWarrantyParentItemId();
            }
        }
      
        $logger->info("hasWarrantyQuoteItem ". json_encode($this->warrantyRelation));


        if (count($this->warrantyRelation) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function needToUpdateWarranty()
    {
        $this->removeLockedWarrantiesFromRelation();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");

        $logger->info("needToUpdateWarranty ++++". json_encode($this->warrantyRelation));

        
        foreach ($this->warrantyRelation as $warrantyItemId => $parentItemId) {
            $parentQuoteItem = $this->cart->getQuote()->getItemById($parentItemId);
            $warrantyQuoteItem = $this->cart->getQuote()->getItemById($warrantyItemId);

            if ($parentQuoteItem->getQty() != $warrantyQuoteItem->getQty()) {
                $this->itemsToUpdate[$warrantyItemId] = $parentQuoteItem->getQty();
            }
        }

        if (count($this->itemsToUpdate) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return $this
     */
    public function updateWarrantyQuoteItems()
    {
        foreach ($this->itemsToUpdate as $warrantyItemId => $qty) {
            $this->cart->getQuote()->getItemById($warrantyItemId)->setQty($qty);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function removeLockedWarrantiesFromRelation()
    {
        $lockedWarrantyIds = $this->checkoutSession->getLockedWarrantyIds();

        //do not want to proceed if no session locked warranty ids exists
        if (!$lockedWarrantyIds || !is_array($lockedWarrantyIds)) {
            return $this;
        }

        foreach ($lockedWarrantyIds as $lockedWarrantyId) {

            //skip if the current warranty id is not in the session locked ids array
            if (!array_key_exists($lockedWarrantyId, $this->warrantyRelation)) {
                continue;
            }

            //collect locked warranties which qty needs to be reduced
            $this->collectWarrantiesReducible($lockedWarrantyId);

            unset($this->warrantyRelation[$lockedWarrantyId]);
        }

        return $this;
    }

    public function collectWarrantiesReducible($warrantyId)
    {
        if (!isset($this->warrantyRelation[$warrantyId])) {
            return $this;
        }

        $warrantyItem = $this->cart->getQuote()->getItemById($warrantyId);
        $parentItem = $this->cart->getQuote()->getItemById($this->warrantyRelation[$warrantyId]);

        if ($parentItem->getQty() < $warrantyItem->getQty()) {
            $this->itemsToUpdate[$warrantyId] = $parentItem->getQty();
        }

        return $this;
    }
}