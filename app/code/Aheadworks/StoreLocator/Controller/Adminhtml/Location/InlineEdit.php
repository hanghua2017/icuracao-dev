<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class InlineEdit.
 */
class InlineEdit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location_save';

    /**
     * @var LocationInterface
     */
    private $location;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Action\Context $context
     * @param LocationRepositoryInterface $locationRepository
     * @param JsonFactory $resultJsonFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        LocationRepositoryInterface $locationRepository,
        JsonFactory $resultJsonFactory,
        DataObjectHelper $dataObjectHelper,
        LoggerInterface $logger
    ) {
        $this->locationRepository = $locationRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return Json
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $locationId) {
            $this->setLocation($this->locationRepository->getById($locationId));

            $this->updateLocation($postItems[$locationId]);
            $this->saveLocation($this->getLocation());
        }

        return $resultJson->setData([
            'messages' => $this->getErrorMessages(),
            'error' => $this->isErrorExists()
        ]);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function updateLocation(array $data)
    {
        $location = $this->getLocation();
        $locationData = $data;
        $this->dataObjectHelper->populateWithArray(
            $location,
            $locationData,
            LocationInterface::class
        );
    }

    /**
     * @param LocationInterface $location
     * @return void
     */
    protected function saveLocation(LocationInterface $location)
    {
        try {
            $this->locationRepository->save($location);
        } catch (InputException $e) {
            $this->getMessageManager()->addError($this->getErrorWithLocationId($e->getMessage()));
            $this->logger->critical($e);
        } catch (LocalizedException $e) {
            $this->getMessageManager()->addError($this->getErrorWithLocationId($e->getMessage()));
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $this->getMessageManager()->addError($this->getErrorWithLocationId('We can\'t save the location.'));
            $this->logger->critical($e);
        }
    }

    /**
     * @return array
     */
    protected function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * @return bool
     */
    protected function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }

    /**
     * @param LocationInterface $location
     * @return $this
     */
    protected function setLocation(LocationInterface $location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return LocationInterface
     */
    protected function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithLocationId($errorText)
    {
        return '[Location ID: ' . $this->getLocation()->getLocationId() . '] ' . __($errorText);
    }
}
