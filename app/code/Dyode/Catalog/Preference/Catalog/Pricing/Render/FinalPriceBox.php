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

namespace Dyode\Catalog\Preference\Catalog\Pricing\Render;

/**
 * Simple Product FinalPriceBox Preference Class
 *
 * Add discount calculation facility
 *
 * @package Dyode\Catalog\Preference\Catalog\Pricing\Render
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{

    /**
     * Calculate discount based on regular price and special price.
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