<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Dyode\ArInvoice\Model\OrderCollection $orderCollection
     **/
    protected $orderCollection;
    /**
     * @var \Dyode\ArInvoice\Helper\Data $_arInvoiceHelper 
     **/
    protected $_arInvoiceHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Model\OrderCollection $orderCollection,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
    ) {
        $this->orderCollection = $orderCollection;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        parent::__construct($context);
    }
    public function execute()
    {   
        // $orders = $this->orderCollection->getSalesOrderCollection();
        $this->orderCollection->createInvoice(3);
        echo "Hello";
        // var_dump($orders);
        die();
    }
}