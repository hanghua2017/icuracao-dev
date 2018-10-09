<?php
/**
 * Created by PhpStorm.
 * User: rajeevktomy
 * Date: 27/09/18
 * Time: 12:14 PM
 */

namespace Dyode\Catalog\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LockWarrantyUpdateInCart implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $info;

    /**
     * @var array
     */
    protected $quoteRelations = [];

    /**
     * @var array
     */
    protected $lockIds = [];

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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $this->info = $observer->getInfo();
        $this->cart = $observer->getCart();

        if ($this->hasWarrantyQuoteItem()) {
            if ($this->checkWarrantyLockingNeeded()) {
                $this->applyWarrantyUpdateLock();
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWarrantyQuoteItem()
    {
        foreach ($this->cart->getItems() as $quoteItem) {
            if ($quoteItem->getWarrantyParentItemId()) {
                $this->quoteRelations[(int)$quoteItem->getWarrantyParentItemId()] = $quoteItem->getItemId();
            }
        }

        if (count($this->quoteRelations) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function checkWarrantyLockingNeeded()
    {
        foreach ($this->quoteRelations as $parentQuoteId => $warrantyQuoteId) {
            $requestWarrantyQty = $this->collectInfoQtyById($warrantyQuoteId);

            if (!$requestWarrantyQty) {
                continue;
            }

            $currentWarrantyQuote = $this->cart->getQuote()->getItemById($warrantyQuoteId);

            if ($currentWarrantyQuote->getQty() != $requestWarrantyQty) {
                $this->lockIds[] = $warrantyQuoteId;
            }
        }

        if (count($this->lockIds) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return $this
     */
    public function applyWarrantyUpdateLock()
    {
        $existingLockIds = $this->checkoutSession->getLockedWarrantyIds() ?
            $this->checkoutSession->getLockedWarrantyIds() : [];

        $this->checkoutSession->setLockedWarrantyIds(array_unique(array_merge($existingLockIds, $this->lockIds)));
        return $this;
    }

    /**
     * @param $quoteItemId
     * @return bool|float
     */
    protected function collectInfoQtyById($quoteItemId)
    {
        foreach ($this->info->getData() as $itemId => $itemInfo) {
            if ($itemId != $quoteItemId) {
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (double)$itemInfo['qty'] : false;
            if ($qty > 0) {
                return $qty;
            }
        }

        return false;
    }
}