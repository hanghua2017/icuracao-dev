<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Region.
 */
class Region implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @param CollectionFactory $regionCollectionFactory
     */
    public function __construct(
        CollectionFactory $regionCollectionFactory
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->options) {
            $this->options = [];

            $options = [];
            $regionsCollection = $this->regionCollectionFactory->create()->load();
            foreach ($regionsCollection as $region) {
                $options[] = ['label' => $region->getName(), 'value' => $region->getRegionId()];
            }
            $this->options = $options;
        }
        $options = $this->options;

        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }

        return $options;
    }
}
