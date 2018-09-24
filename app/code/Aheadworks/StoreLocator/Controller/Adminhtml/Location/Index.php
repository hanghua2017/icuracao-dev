<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index.
 */
class Index extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location';

    /**
     * @return Page|Forward
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Aheadworks_StoreLocator::location');
        $resultPage->getConfig()->getTitle()->prepend(__('Locations'));

        $resultPage->addBreadcrumb(__('Locations'), __('Locations'));
        $resultPage->addBreadcrumb(__('Manage Locations'), __('Manage Locations'));

        return $resultPage;
    }
}
