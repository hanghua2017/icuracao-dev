<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            F+QH6f8gWEYP7MDThQK5sY3nVEIJTKrEeZ4at/WUMj4=
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Block/Adminhtml/Destination.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Block\Adminhtml;

class Destination extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('Add New Destination');
        parent::_construct();
    }
}
