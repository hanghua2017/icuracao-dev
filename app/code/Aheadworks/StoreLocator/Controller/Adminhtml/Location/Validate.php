<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Error;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Object;
use Magento\Framework\Validator\Exception;

/**
 * Class Validate.
 */
class Validate extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location_save';

    /**
     * @param Object $response
     * @return LocationInterface|null
     * @throws LocalizedException
     */
    protected function validateLocation($response)
    {
        $location = null;
        $errors = null;

        try {
            $locationData = $this->getRequest()->getParam('location');

            $location = $this->locationDataFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $location,
                $locationData,
                LocationInterface::class
            );

            $errors = $this->locationManagement->validate($location)->getMessages();
        } catch (Exception $exception) {
            /* @var $error Error */
            foreach ($exception->getMessages(MessageInterface::TYPE_ERROR) as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $messages[] = $error;
                }
            }

            $response->setMessages($messages);
            $response->setError(1);
        }

        return $location;
    }

    /**
     * @return Json
     * @throws LocalizedException
     */
    public function execute()
    {
        $response = new DataObject();
        $response->setError(0);

        $location = $this->validateLocation($response);

        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessage($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
