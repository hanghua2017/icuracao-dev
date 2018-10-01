<?php

namespace Dyode\InventoryUpdate\Cron;
 
class Inventoryupdate
{
	protected $logger;
 
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface
	) {
		$this->logger = $loggerInterface;
	}
 
	public function execute() {

		//test command line
        //php bin/magento cron:run --group="dyode_inventoryupdate_cron_group"
		//$this->logger->debug('Dyode\InventoryUpdate\Cron\Inventoryupdate');

	}
}