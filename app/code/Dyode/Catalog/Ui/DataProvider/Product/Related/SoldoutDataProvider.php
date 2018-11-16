<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Ui\DataProvider\Product\Related;

use Dyode\Catalog\Model\Product\Link;
use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;

class SoldoutDataProvider extends AbstractDataProvider
{

    /**
     * @return string
     */
    protected function getLinkType()
    {
        return Link::LINK_CODE;
    }
}
