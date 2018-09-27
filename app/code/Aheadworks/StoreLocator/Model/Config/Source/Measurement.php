<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Measurement.
 */
class Measurement implements ArrayInterface
{
    const KILOMETERS = 'km';
    const MILES = 'mi';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            self::KILOMETERS => __('kilometers'),
            self::MILES => __('miles'),
        ];
    }
}
