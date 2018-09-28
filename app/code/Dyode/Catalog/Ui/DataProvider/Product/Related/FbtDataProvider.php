<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Ui\DataProvider\Product\Related;

use Dyode\Catalog\Model\Product\Link;
use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;

class FbtDataProvider extends AbstractDataProvider
{

    /**
     * @return string
     */
    protected function getLinkType()
    {
        return Link::LINK_CODE;
    }
}
