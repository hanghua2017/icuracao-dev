<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Location\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs.
 */
class Tabs extends WidgetTabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('location_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Location Information'));
    }
}
