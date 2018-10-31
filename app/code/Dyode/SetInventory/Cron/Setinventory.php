<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\SetInventory\Cron;

/**
 * SetInventory Cron
 * @category Dyode
 * @package  Dyode_SetInventory
 * @module   SetInventory
 * @author   Nithin
 */
class Setinventory
{
    protected $update;

    /**
    * constructor function
    */
    public function __construct(
        \Dyode\SetInventory\Model\Update $update
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
