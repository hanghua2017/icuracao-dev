<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Controller\Test;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $orderCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Model\OrderCollection $orderCollection
    ) {
        $this->orderCollection = $orderCollection;
        parent::__construct($context);
    }
    public function execute()
    {   
        // $orders = $this->orderCollection->getSalesOrderCollection();
        $this->orderCollection->createInvoice(2);
        // var_dump($orders);
        die();
    }
}