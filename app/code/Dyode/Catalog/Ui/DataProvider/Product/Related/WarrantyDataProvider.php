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

class WarrantyDataProvider extends AbstractDataProvider
{

    /**
     * @return string
     */
    protected function getLinkType()
    {
        return Link::WARRANTY_CODE;
    }

    /**
     * Add product type filtering. We need only virtual type products for the selection.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        $collection->addAttributeToFilter('type_id', ['eq' => 'virtual']);
        return $collection;
    }
}
