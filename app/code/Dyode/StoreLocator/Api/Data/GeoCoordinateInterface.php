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

interface GeoCoordinateInterface
{

    const CITY = 'city';
    const ZIP = 'zip';
    const LAT = 'lat';
    const COUNTY = 'county';
    const LNG = 'lng';
    const GEOCOORDINATE_ID = self::ZIP;
    const STATE = 'state';
    const ABBR = 'abbr';

    /**
     * Get geocoordinate_id
     *
     * @return string|null
     */
    public function getGeocoordinateId();

    /**
     * Set geocoordinate_id
     *
     * @param string $geocoordinateId
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setGeocoordinateId($geocoordinateId);

    /**
     * Get zip
     *
     * @return string|null
     */
    public function getZip();

    /**
     * Set zip
     *
     * @param string $zip
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setZip($zip);

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     *
     * @param string $city
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setCity($city);

    /**
     * Get state
     *
     * @return string|null
     */
    public function getState();

    /**
     * Set state
     *
     * @param string $state
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setState($state);

    /**
     * Get abbr
     *
     * @return string|null
     */
    public function getAbbr();

    /**
     * Set abbr
     *
     * @param string $abbr
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setAbbr($abbr);

    /**
     * Get county
     *
     * @return string|null
     */
    public function getCounty();

    /**
     * Set county
     *
     * @param string $county
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setCounty($county);

    /**
     * Get lat
     *
     * @return string|null
     */
    public function getLat();

    /**
     * Set lat
     *
     * @param string $lat
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setLat($lat);

    /**
     * Get lng
     *
     * @return string|null
     */
    public function getLng();

    /**
     * Set lng
     *
     * @param string $lng
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function setLng($lng);
}
