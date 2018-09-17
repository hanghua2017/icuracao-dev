<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

use Aheadworks\StoreLocator\Api\Data\ValidationResultsInterfaceFactory;
use Aheadworks\StoreLocator\Api\LocationManagementInterface;
use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Class LocationManagement.
 */
class LocationManagement implements LocationManagementInterface
{
    /**
     * @var LocationFactory
     */
    private $locationFactory;

    /**
     * @var ValidationResultsInterfaceFactory
     */
    private $validationResultsDataFactory;

    /**
     * @var LocationValidator
     */
    private $validator;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param LocationFactory $locationFactory
     * @param LocationValidator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     */
    public function __construct(
        LocationFactory $locationFactory,
        LocationValidator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory
    ) {
        $this->locationFactory = $locationFactory;
        $this->validator = $validator;
        $this->validationResultsDataFactory = $validationResultsDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(LocationInterface $location)
    {
        $locationErrors = $this->validator->isValid($location);

        $validationResults = $this->validationResultsDataFactory->create();
        if ($locationErrors !== true) {
            return $validationResults->setIsValid(false)
                ->setMessages($this->validator->getMessages());
        }

        return $validationResults->setIsValid(true)
            ->setMessages([]);
    }
}
