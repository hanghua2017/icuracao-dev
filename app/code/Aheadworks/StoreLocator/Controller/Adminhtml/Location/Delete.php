<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class Delete.
 */
class Delete extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location_delete';

    /**
     * @return Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $locationId = $this->initLocation();

        if (!empty($locationId)) {
            try {
                $this->locationRepository->deleteById($locationId);
                $this->messageManager->addSuccess(__('You deleted the location.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('aw_store_locator/location/index');
    }
}
