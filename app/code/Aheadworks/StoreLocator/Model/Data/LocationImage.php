<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Data;

use Aheadworks\StoreLocator\Api\Data\LocationImageInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class LocationImage.
 */
class LocationImage extends AbstractSimpleObject implements LocationImageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelete()
    {
        return $this->_get(self::DELETE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setDelete($delete)
    {
        return $this->setData(self::DELETE, $delete);
    }
}
