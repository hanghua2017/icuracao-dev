<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TopMenu.
 */
class TopMenu implements OptionSourceInterface
{
    const MENU_ITEM_DISABLED = 0;
    const MENU_ITEM_LEFT_ALIGN = 1;
    const MENU_ITEM_RIGHT_ALIGN = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            self::MENU_ITEM_DISABLED => __('Disabled'),
            self::MENU_ITEM_LEFT_ALIGN => __('Enabled (First Item)'),
            self::MENU_ITEM_RIGHT_ALIGN => __('Enabled (Last Item)'),
        ];
    }
}
