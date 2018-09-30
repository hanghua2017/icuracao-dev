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

namespace Dyode\StoreLocator\Api;

interface GeoCoordinateRepositoryInterface
{

    /**
     * Save GeoCoordinate
     *
     * @param \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface $geoCoordinate
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface $geoCoordinate
    );

    /**
     * Retrieve GeoCoordinate
     *
     * @param string $geocoordinateId
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($geocoordinateId);

    /**
     * Retrieve GeoCoordinate matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GeoCoordinate
     *
     * @param \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface $geoCoordinate
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface $geoCoordinate
    );

    /**
     * Delete GeoCoordinate by ID
     *
     * @param string $geocoordinateId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($geocoordinateId);
}
