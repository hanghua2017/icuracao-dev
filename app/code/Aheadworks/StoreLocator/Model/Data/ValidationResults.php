<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Data;

use Aheadworks\StoreLocator\Api\Data\ValidationResultsInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class ValidationResults.
 */
class ValidationResults extends AbstractSimpleObject implements ValidationResultsInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->_get(self::VALID);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->_get(self::MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsValid($isValid)
    {
        return $this->setData(self::VALID, $isValid);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessages(array $messages)
    {
        return $this->setData(self::MESSAGES, $messages);
    }
}
