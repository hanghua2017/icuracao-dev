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
namespace Dyode\PriceFormat\Plugin\Framework;

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
    public function beforeformatTxt($price, $options = [])
    {
        $options['locale'] = 'en';
        return $this;
    }
}