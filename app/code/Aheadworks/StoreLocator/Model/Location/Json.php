<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Location;

use Aheadworks\StoreLocator\Helper\Image;
use Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection;
use Magento\Framework\Json\Encoder;

/**
 * Class Json.
 */
class Json
{
    /**
     * @var Image
     */
    protected $helperImage;

    /**
     * @var Encoder
     */
    protected $jsonEncoder;

    /**
     * @param Image $helperImage
     * @param Encoder $jsonEncoder
     */
    public function __construct(
        Image $helperImage,
        Encoder $jsonEncoder
    ) {
        $this->helperImage = $helperImage;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Retrieve location data JSON.
     *
     * @param Collection $locationCollection
     * @return string
     */
    public function getDataJson(Collection $locationCollection)
    {
        $locationsItems = [];
        foreach ($locationCollection as $location) {
            $locationData = $location->getData();

            $locationData['image'] = $this->helperImage->getImagePath($location->getImage());
            $locationData['custom_marker'] = $this->helperImage->getImagePath($location->getCustomMarker());

            $locationsItems[] = $locationData;
        }

        return $this->jsonEncoder->encode($locationsItems);
    }
}
