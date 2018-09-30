<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block;

use Aheadworks\StoreLocator\Helper\Image;
use Aheadworks\StoreLocator\Model\Location\Json;
use Magento\Framework\Json\Encoder;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Location.
 */
class Location extends Template
{
    /**
     * @var AbstractCollection
     */
    protected $collection = null;

    /**
     * @var Image
     */
    protected $helperImage;

    /**
     * @var Json
     */
    protected $locationJson;

    /**
     * @var Encoder
     */
    protected $jsonEncoder;

    /**
     * Construct
     *
     * @param Context $context
     * @param Image $helperImage
     * @param Json $locationJson
     * @param Encoder $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Image $helperImage,
        Json $locationJson,
        Encoder $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperImage = $helperImage;
        $this->locationJson = $locationJson;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @param AbstractCollection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getLocationDataJson()
    {
        return $this->locationJson->getDataJson($this->getCollection());
    }
}
