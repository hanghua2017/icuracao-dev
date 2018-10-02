<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Preference\ConfigurableProduct\Pricing\Render;

/**
 * Configurable Product FinalPriceBox Preference Class
 *
 * Add discount calculation facility.
 *
 * @package Dyode\Catalog\Preference\ConfigurableProduct\Pricing\Render
 */
class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{

    /**
     * Use to calculate discount based on the regular price and special price.
     *
     * @param $regularPriceModel
     * @param $finalPriceModel
     * @return float|string
     */
    public function discount($regularPriceModel, $finalPriceModel)
    {
        $rp = floatval((string)$regularPriceModel->getAmount()); //regular price
        $fp = floatval((string)$finalPriceModel->getAmount()); //final price

        if ($rp && $fp) {
            return round((($rp - $fp)/$rp)*100);
        }
        return '';
    }
}