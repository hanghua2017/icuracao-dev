<?php
/**
 * Dyode_StoreLocator Magento2 Module.
 *
 * Extending Aheadworks_StoreLocator
 *
 * @package   Dyode
 * @module    Dyode_StoreLocator
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\StoreLocator\Model\ResourceModel;

class GeoCoordinate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('locations', 'zip');
    }
}
