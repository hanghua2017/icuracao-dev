<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\InventoryUpdate\Cron;
 
/**
 * Non-Set InventoryUpdate Cron
 * @category Dyode
 * @package  Dyode_InventoryUpdate
 * @module   InventoryUpdate
 * @author   Nithin
 */
class Inventoryupdate
{
	protected $logger;

	protected $inventory;

    /**
	 * cron constructor
	*/
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface,
		\Dyode\InventoryUpdate\Model\Inventory $inventory
	) {
		$this->logger = $loggerInterface;
		$this->inventory = $inventory;
	}
 
	/**
	 * inventory cron execute function
	*/
	public function execute() {
		$this->inventory->updateInventory();
		$this->logger->debug('Dyode\InventoryUpdate\Cron\Inventoryupdate');
	}
}