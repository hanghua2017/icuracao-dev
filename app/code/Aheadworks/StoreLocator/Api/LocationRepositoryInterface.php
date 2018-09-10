<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Api;

use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Location CRUD interface.
 *
 * @api
 */
interface LocationRepositoryInterface
{
    /**
     * Create location.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationInterface $location
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(LocationInterface $location);

    /**
     * Retrieve location.
     *
     * @param string $locationId
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($locationId);

    /**
     * Retrieve location.
     *
     * @param int $locationId
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($locationId);

    /**
     * Retrieve location which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\StoreLocator\Api\Data\LocationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete location.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationInterface $location
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(LocationInterface $location);

    /**
     * Delete location by ID.
     *
     * @param int $locationId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($locationId);
}
