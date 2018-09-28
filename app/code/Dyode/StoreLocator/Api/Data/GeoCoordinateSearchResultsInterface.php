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

namespace Dyode\StoreLocator\Api\Data;

interface GeoCoordinateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GeoCoordinate list.
     *
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface[]
     */
    public function getItems();

    /**
     * Set zip list.
     *
     * @param \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
