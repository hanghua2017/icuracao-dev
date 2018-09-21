<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Location image interface.
 *
 * @api
 */
interface LocationImageInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     */
    const VALUE = 'value';
    const DELETE = 'delete';
    /**#@-*/

    /**
     * Get location image value.
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Set location image value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get location image delete status.
     *
     * @return int|null
     */
    public function getDelete();

    /**
     * Set location image delete status.
     *
     * @param int $delete
     * @return $this
     */
    public function setDelete($delete);
}
