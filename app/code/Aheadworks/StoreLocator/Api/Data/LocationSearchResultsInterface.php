<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for location search results.
 *
 * @api
 */
interface LocationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get locations list.
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * Set locations list.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
