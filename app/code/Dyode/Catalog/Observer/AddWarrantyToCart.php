<?php
/**
 * Dyode_Checkout Magento2 Module.
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
use Magento\Checkout\Model\Cart as CustomerCart;

/**
 * AddWarrantyToCart Observer
 */
class AddWarrantyToCart implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * AddWarrantyToCart constructor.
     *
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(CustomerCart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Add warranty to the cart if the warranty is selected.
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $quote = $this->cart->getQuote();
        $parentQuoteItem = $quote->getItemByProduct($product);
        $warrantyQuoteItem = $parentQuoteItem->getWarrantyChildItem();
        $warrantyQuoteItem->setWarrantyParentItemId((int)$parentQuoteItem->getItemId())->save();
    }
}