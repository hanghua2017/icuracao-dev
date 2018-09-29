<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Aheadworks\StoreLocator\Api\Data\LocationImageInterface;
use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Aheadworks\StoreLocator\Controller\RegistryConstants;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\Exception;

/**
 * Class Save.
 */
class Save extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location_save';

    /**
     * @return Redirect
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();

        $locationId = isset($originalRequestData['location']['location_id'])
            ? $originalRequestData['location']['location_id']
            : null;
        if ($originalRequestData) {
            try {
                $locationData = $originalRequestData['location'];

                $location = $this->locationDataFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $location,
                    $locationData,
                    LocationInterface::class
                );

                // assign stores
                $location->setStores($locationData['stores']);

                // assign image field values to location data
                $location->setImageAdditionalData(
                    $this->processImageAdditionalData('image', $originalRequestData)
                );
                $location->setCustomMarkerAdditionalData(
                    $this->processImageAdditionalData('custom_marker', $originalRequestData)
                );

                $location = $this->locationRepository->save($location);
                $locationId = $location->getLocationId();

                $this->coreRegistry->register(RegistryConstants::CURRENT_LOCATION_ID, $locationId);
                $this->messageManager->addSuccess(__('You saved the location.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->addSessionErrorMessages($messages);
                $this->_getSession()->setLocationData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setLocationData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception, __('An error occurred while saving the location.'));
                $this->_getSession()->setLocationData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($locationId) {
                $resultRedirect->setPath(
                    'aw_store_locator/location/edit',
                    ['location_id' => $locationId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'aw_store_locator/location/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('aw_store_locator/location/index');
        }
        return $resultRedirect;
    }

    /**
     * Process uploaded image.
     *
     * @param string $fieldId
     * @param mixed $originalRequestData
     * @return LocationImageInterface
     */
    public function processImageAdditionalData($fieldId, $originalRequestData)
    {
        $value = isset($originalRequestData[$fieldId]['value']) ? $originalRequestData[$fieldId]['value'] : null;
        $delete = isset($originalRequestData[$fieldId]['delete']) ? $originalRequestData[$fieldId]['delete'] : null;
        $locationImageData = [
            'value' => $value,
            'delete' => $delete,
        ];

        $locationImageDataObject = $this->locationImageDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $locationImageDataObject,
            $locationImageData,
            LocationImageInterface::class
        );

        return $locationImageDataObject;
    }
}
