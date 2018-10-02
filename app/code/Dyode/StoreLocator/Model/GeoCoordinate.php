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

namespace Dyode\StoreLocator\Model;

use Dyode\StoreLocator\Api\Data\GeoCoordinateInterface;

class GeoCoordinate extends \Magento\Framework\Model\AbstractModel implements GeoCoordinateInterface
{

    protected $_eventPrefix = 'dyode_storelocator_geocoordinate';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate::class);
    }

    /**
     * Get geocoordinate_id
     *
     * @return string
     */
    public function getGeocoordinateId()
    {
        return $this->getData(self::GEOCOORDINATE_ID);
    }

    /**
     * Set geocoordinate_id
     *
     * @param string $geocoordinateId
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setGeocoordinateId($geocoordinateId)
    {
        return $this->setData(self::GEOCOORDINATE_ID, $geocoordinateId);
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->getData(self::ZIP);
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setZip($zip)
    {
        return $this->setData(self::ZIP, $zip);
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     *
     * @param string $city
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * Set state
     *
     * @param string $state
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Get abbr
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->getData(self::ABBR);
    }

    /**
     * Set abbr
     *
     * @param string $abbr
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setAbbr($abbr)
    {
        return $this->setData(self::ABBR, $abbr);
    }

    /**
     * Get county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->getData(self::COUNTY);
    }

    /**
     * Set county
     *
     * @param string $county
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setCounty($county)
    {
        return $this->setData(self::COUNTY, $county);
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->getData(self::LAT);
    }

    /**
     * Set lat
     *
     * @param string $lat
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setLat($lat)
    {
        return $this->setData(self::LAT, $lat);
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->getData(self::LNG);
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setLng($lng)
    {
        return $this->setData(self::LNG, $lng);
    }
}
