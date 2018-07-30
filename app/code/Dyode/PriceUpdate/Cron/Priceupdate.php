<?php

namespace Dyode\PriceUpdate\Cron;
 
class Priceupdate
{
	protected $logger;
 
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface
	) {
		$this->logger = $loggerInterface;
	}
 
	public function execute() {

		//test command line
        //php bin/magento cron:run --group="dyode_priceupdate_cron_group"
		//$this->logger->debug('Dyode\PriceUpdate\Cron\Priceupdate');

	}
}