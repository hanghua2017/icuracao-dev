<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\ProcessEstimate\Cron;
 
/**
 * ProcessEstimate Cron
 * @category Dyode
 * @package  Dyode_ProcessEstimate
 * @module   ProcessEstimate
 * @author   Nithin
 */
class Processestimate
{
	protected $logger;

	protected $estimate;
 
	/**
	 * constructor function
	*/
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface,
		\Dyode\ProcessEstimate\Model\Estimate $estimate
	) {
		$this->logger = $loggerInterface;
		$this->estimate = $estimate;
	}
 
	/**
	 * cron execute function
	*/
	public function execute() {
		$this->estimate->getOrders();
		$this->logger->debug('Dyode\ProcessEstimate\Cron\Processestimate');
	}
}