<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Edit.
 */
class Edit extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location';

    /**
     * @return Page|Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        $locationId = $this->initLocation();

        $location = $this->locationFactory->create();
        $isExistingLocation = (bool)$locationId;
        if ($isExistingLocation) {
            try {
                $location->load($locationId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException($e, __('An error occurred while editing the location.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('aw_store_locator/location/index');
                return $resultRedirect;
            }
        }

        $this->coreRegistry->register('aheadworks_location', $location);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_StoreLocator::store_locator');
        $this->prepareDefaultLocationTitle($resultPage);
        $resultPage->setActiveMenu('Aheadworks_StoreLocator::location');
        if ($isExistingLocation) {
            $resultPage->getConfig()->getTitle()->prepend($location->getTitle());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Location'));
        }
        return $resultPage;
    }
}
