<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dyode\PriceFilter\Model\Currency;

/**
 * Currency model
 *
 */
class Currency extends \Magento\Directory\Model\Currency
{
    /**
     * @param float $price
     * @param array $options
     * @return string
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
        $price = sprintf("%F", $price);
        return $this->_localeCurrency->getCurrency($this->getCode())->toCurrency($price, $options);
    }
}
