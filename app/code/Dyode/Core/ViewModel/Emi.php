<?php
/**
 * Dyode_Core Magento2 Module.
 *
 * Holds common functionalities of Dyode modules.
 *
 * @package Dyode
 * @module  Dyode_Core
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\Core\ViewModel;

use Magento\Checkout\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Emi Suggestion View Model
 */
class Emi implements ArgumentInterface
{

    const APPLY_NOW_CONFIG_PATH = 'curacao_catalog/curacao_product_page/emi_link';

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

    protected $_scopeConfig;

    /**
     * Emi constructor.
     *
     * @param \Magento\Checkout\Helper\Data                      $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(Data $helper, ScopeConfigInterface $scopeConfig)
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
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
    public function monthlyEmi($price)
    {
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

    /**
     * Apply Now external link from system configuration.
     *
     * @return string|boolean
     */
    public function applyNowLink()
    {
        $applyLink = $this->_scopeConfig->getValue(self::APPLY_NOW_CONFIG_PATH, ScopeInterface::SCOPE_STORE);

        if ($applyLink) {
            return $applyLink;
        }

        return false;
    }
}
