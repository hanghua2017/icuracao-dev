<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\SetEcommerceData\Cron;

/**
 * SetEcommerceData Cron
 * @category Dyode
 * @package  Dyode_SetEcommerceData
 * @module   SetEcommerceData
 * @author   Nithin
 */
class SetEcommerceData
{
    protected $update;

    /**
    * constructor function
    */
    public function __construct(
        \Dyode\SetEcommerceData\Model\Product $update
    ) {
        $this->update = $update;
    }
    /**
     * cron execute function
     */
    public function execute()
    {
        $this->update->setInventoryUpdate();
    }
}