<?php
/**
 * Copyright © Dyode
 */

namespace Dyode\Checkout\ViewModel\Cart;


use Magento\Checkout\Helper\Data;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Emi Suggestion View Model
 */
class Emi implements ArgumentInterface
{
    /**
     * Checkout helper
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_helper;

    /**
     * Can be a product, cart item; change by the context.
     *
     * @var object
     */
    protected $_item;

    /**
     * Emi constructor.
     *
     * @param \Magento\Checkout\Helper\Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Set Item.
     *
     * @param object $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get the item.
     *
     * @return object
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Checks the item is eligible for emi.
     *
     * @return bool
     */
    public function hasEmi()
    {
        return (bool)$this->monthlyEmi($this->getItem()->getCalculationPrice());
    }

    /**
     * Checkout helper
     *
     * @return \Magento\Checkout\Helper\Data
     */
    public function helper()
    {
        return $this->_helper;
    }

    /**
     * Provide monthly emi rate based on the item price.
     *
     * @return float|int
     */
    public function monthlyEmi()
    {
        $price = $this->getItem()->getCalculationPrice();

        switch ($price) {
            case $price > 1000:
                return $price * 0.05;
            case ($price > 500 && $price <= 1000):
                return $price * 0.075;
            case ($price > 200 && $price <= 500):
                return $price * 0.1;
            case ($price > 40 && $price <= 200):
                return 20;
            default:
                return 0;
        }
    }
}
