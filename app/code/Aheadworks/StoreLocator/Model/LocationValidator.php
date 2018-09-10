<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\AbstractValidator;
use Zend_Validate_Exception;

/**
 * Class LocationValidator.
 */
class LocationValidator extends AbstractValidator
{
    /**
     * @var LocationRegistry
     */
    protected $locationRegistry;

    /**
     * @param LocationRegistry $locationRegistry
     */
    public function __construct(LocationRegistry $locationRegistry)
    {
        $this->locationRegistry = $locationRegistry;
    }

    /**
     * Validate location model.
     *
     * @param Location $value
     * @return boolean
     * @throws Zend_Validate_Exception
     * @throws LocalizedException
     */
    public function isValid($value)
    {
        $messages = [];

        if (!\Zend_Validate::is(trim($value->getStatus()), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`Status`'));
        }

        if (!\Zend_Validate::is(trim($value->getTitle()), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`Title`'));
        }

        if (!\Zend_Validate::is(trim($value->getCountryId()), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`Country`'));
        }

        if (!\Zend_Validate::is(trim($value->getCity()), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`City`'));
        }

        if (!\Zend_Validate::is(trim($value->getStreet()), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`Street`'));
        }

        if (!\Zend_Validate::is($value->getStores(), 'NotEmpty')) {
            $this->addErrorMessage($messages, InputException::requiredField('`Store View`'));
        }

        $this->_addMessages($messages);
        return empty($messages);
    }

    /**
     * Format error message.
     *
     * @param string[] $messages
     * @param string $message
     * @param array $params
     * @return void
     */
    protected function addErrorMessage(&$messages, $message, $params)
    {
        $messages[$params['fieldName']] = __($message, $params);
    }
}
