<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Magento\Framework\View\Result\Layout;

/**
 * Class Grid.
 */
class Grid extends AbstractIndex
{
    /**
     * @return Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
