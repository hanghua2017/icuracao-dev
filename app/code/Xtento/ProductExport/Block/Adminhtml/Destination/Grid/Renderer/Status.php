<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            F+QH6f8gWEYP7MDThQK5sY3nVEIJTKrEeZ4at/WUMj4=
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Block/Adminhtml/Destination/Grid/Renderer/Status.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Block\Adminhtml\Destination\Grid\Renderer;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render destination status
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return __('Used in <strong>%1</strong> profile(s)', count($row->getProfileUsage()));
    }
}
