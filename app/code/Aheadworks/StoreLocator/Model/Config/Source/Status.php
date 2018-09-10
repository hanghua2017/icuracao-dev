<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status.
 */
class Status implements OptionSourceInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ([
             self::STATUS_ENABLED => __('Enabled'),
             self::STATUS_DISABLED => __('Disabled')
         ] as $key => $value) {
            $options[] = ['label' => $value, 'value' => $key];
        }
        return $options;
    }
}
