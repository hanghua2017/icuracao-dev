<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Controller\Index;

/**
 * Price Update Contoller
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class Index extends \Magento\Framework\App\Action\Action
{
    public $priceModel;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Dyode\PriceUpdate\Model\PriceUpdate
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\PriceUpdate\Model\PriceUpdate $priceModel
    ) {
        $this->priceModel = $priceModel;
        parent::__construct($context);
    }

    /**
     * function name : execute
     * definition : this function is used for testing the price update
     * @return no return
     */
    public function execute()
    {
        //uncomment the below line and run the controller url for testing
        $this->priceModel->updatePrice();
    }
}
