<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Location interface.
 *
 * @api
 */
interface LocationInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const LOCATION_ID = 'location_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';
    const COUNTRY_ID = 'country_id';
    const REGION_ID = 'region_id';
    const CITY = 'city';
    const STREET = 'street';
    const ZIP = 'zip';
    const PHONE = 'phone';
    const ZOOM = 'zoom';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const IMAGE = 'image';
    const CUSTOM_MARKER = 'custom_marker';
    const IMAGE_ADDITIONAL_DATA = 'image_additional_data';
    const CUSTOM_MARKER_ADDITIONAL_DATA = 'custom_marker_additional_data';
    const STORES = 'stores';
    /**#@-*/

    /**
     * Get location id.
     *
     * @return int|null
     */
    public function getLocationId();

    /**
     * Set location id.
     *
     * @param int $id
     * @return $this
     */
    public function setLocationId($id);

    /**
     * Get location title.
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set location title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get location code name.
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set location code name.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get location status.
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set location status.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get location sort order.
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set location sort order.
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get location country.
     *
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set location country.
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Get location state.
     *
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set location state.
     *
     * @param string $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Get location city.
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Set location city.
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Get location street.
     *
     * @return string|null
     */
    public function getStreet();

    /**
     * Set location street.
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street);

    /**
     * Get location zip.
     *
     * @return string|null
     */
    public function getZip();

    /**
     * Set location zip.
     *
     * @param string $zip
     * @return $this
     */
    public function setZip($zip);

    /**
     * Get location phone.
     *
     * @return string|null
     */
    public function getPhone();

    /**
     * Set location phone.
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone);

    /**
     * Get location zoom.
     *
     * @return int|null
     */
    public function getZoom();

    /**
     * Set location zoom.
     *
     * @param int $zoom
     * @return $this
     */
    public function setZoom($zoom);

    /**
     * Get location latitude.
     *
     * @return float|null
     */
    public function getLatitude();

    /**
     * Set location latitude.
     *
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * Get location longitude.
     *
     * @return float|null
     */
    public function getLongitude();

    /**
     * Set location longitude.
     *
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * Get location image.
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Set location image.
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get location custom google map marker.
     *
     * @return string|null
     */
    public function getCustomMarker();

    /**
     * Set location custom google map marker.
     *
     * @param string $customMarker
     * @return $this
     */
    public function setCustomMarker($customMarker);

    /**
     * Get location image.
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationImageInterface|null
     */
    public function getImageAdditionalData();

    /**
     * Set location image.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationImageInterface $image
     * @return $this
     */
    public function setImageAdditionalData($image);

    /**
     * Get location custom google map marker.
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationImageInterface|null
     */
    public function getCustomMarkerAdditionalData();

    /**
     * Set location custom google map marker.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationImageInterface $customMarker
     * @return $this
     */
    public function setCustomMarkerAdditionalData(LocationImageInterface $customMarker);

    /**
     * Get location stores.
     *
     * @return string[]|null
     */
    public function getStores();

    /**
     * Set location stores.
     *
     * @param string[] $stores
     * @return $this
     */
    public function setStores($stores);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\StoreLocator\Api\Data\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(LocationExtensionInterface $extensionAttributes);
}
