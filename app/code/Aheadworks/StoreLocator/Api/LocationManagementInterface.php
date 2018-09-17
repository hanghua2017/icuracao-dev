<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Api;

use Aheadworks\StoreLocator\Api\Data\LocationInterface;

/**
 * Interface for managing locations accounts.
 *
 * @api
 */
interface LocationManagementInterface
{
    /**
     * Validate location data.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationInterface $location
     * @return \Aheadworks\StoreLocator\Api\Data\ValidationResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(LocationInterface $location);
}
