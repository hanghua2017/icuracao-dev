<?php
/**
 * Dyode_PriceFormat Magento2 Module.
 *
 * Override Magento Price Format
 *
 * @package Dyode
 * @module  Dyode_PriceFormat
 * @author  Nithin <nithin@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\PriceFormat\Model;

/**
 * Currency model
 *
 */
class Currency extends \Magento\Framework\Currency {

    /**
     * @param float $price
     * @param array $options
     * @return object
     */
    public function formatTxt($price, $options = [])
    {
        if (!is_numeric($price)) {
            $price = $this->_localeFormat->getNumber($price);
        }
        /**
         * Fix problem with 12 000 000, 1 200 000
         *
         * %f - the argument is treated as a float, and presented as a floating-point number (locale aware).
         * %F - the argument is treated as a float, and presented as a floating-point number (non-locale aware).
         */
        $options["locale"] = 'en';
        $price = sprintf("%F", $price);
        return $this->_localeCurrency->getCurrency($this->getCode())->toCurrency($price, $options);
    }
}