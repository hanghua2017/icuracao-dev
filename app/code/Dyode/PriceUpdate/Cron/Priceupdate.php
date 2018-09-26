<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Cron;
 
/**
 * Priceupdate Cron
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class Priceupdate
{
 	/**
	 * constructor function
	 */
    public function __construct(
        \Dyode\PriceUpdate\Model\PriceUpdate $priceModel
    ) {
        $this->_priceModel = $priceModel;
    }
    /**
	 * cron execute function
	 */
    public function execute()
    {
        $this->_priceModel->updatePrice();
    }
}
