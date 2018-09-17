<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Data;

use Aheadworks\StoreLocator\Api\Data\LocationExtensionInterface;
use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Aheadworks\StoreLocator\Api\Data\LocationImageInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Location.
 */
class Location extends AbstractExtensibleModel implements LocationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * {@inheritdoc}
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * {@inheritdoc}
     */
    public function getZip()
    {
        return $this->getData(self::ZIP);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function getZoom()
    {
        return $this->getData(self::ZOOM);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomMarker()
    {
        return $this->getData(self::CUSTOM_MARKER);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageAdditionalData()
    {
        return $this->getData(self::IMAGE_ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomMarkerAdditionalData()
    {
        return $this->getData(self::CUSTOM_MARKER_ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->getData(self::STORES);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * {@inheritdoc}
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * {@inheritdoc}
     */
    public function setZip($zip)
    {
        return $this->setData(self::ZIP, $zip);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * {@inheritdoc}
     */
    public function setZoom($zoom)
    {
        return $this->setData(self::ZOOM, $zoom);
    }

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomMarker($customMarker)
    {
        return $this->setData(self::CUSTOM_MARKER, $customMarker);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageAdditionalData($image)
    {
        return $this->setData(self::IMAGE_ADDITIONAL_DATA, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomMarkerAdditionalData(LocationImageInterface $customMarker)
    {
        return $this->setData(self::CUSTOM_MARKER_ADDITIONAL_DATA, $customMarker);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(LocationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
