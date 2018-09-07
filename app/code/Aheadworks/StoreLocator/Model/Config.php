<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

/**
 * Class Config.
 */
class Config
{
    /**
     * Default location country.
     */
    const DEFAULT_LOCATION_COUNTRY = 'US';

    /**
     * Default location state/region.
     */
    const DEFAULT_LOCATION_REGION = '1';

    /**
     * Default Google Maps zoom.
     */
    const DEFAULT_ZOOM = '12';

    /**
     * Default Google Maps latitude.
     */
    const DEFAULT_LATITUDE = '40.446947';

    /**
     * Default Google Maps longitude.
     */
    const DEFAULT_LONGITUDE = '-101.425781';

    /**
     * Default kilometers ratio.
     */
    const DEFAULT_KILOMETERS_RATIO = 6371;

    /**
     * Default miles ratio.
     */
    const DEFAULT_MILES_RATIO = 3959;

    /**
     * Media path.
     */
    const AHEADWORKS_STORE_LOCATOR_MEDIA_PATH = 'aheadworks/store_locator';

    /**
     * Location location folder.
     */
    const AHEADWORKS_STORE_LOCATOR_LOCATION_DIRECTORY = 'location';
}
