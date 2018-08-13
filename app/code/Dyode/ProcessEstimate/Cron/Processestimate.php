<?php

namespace Dyode\ProcessEstimate\Cron;
 
class Processestimate
{
	protected $logger;
 
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface
	) {
		$this->logger = $loggerInterface;
	}
 
	public function execute() {

		//test command line
        //php bin/magento cron:run --group="dyode_processestimate_cron_group"
		//$this->logger->debug('Dyode\ProcessEstimate\Cron\Processestimate');

	}
}