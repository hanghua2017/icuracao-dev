<?php
namespace Dyode\Customerstatus\Controller\Index;

/**
 * CustomerStatus Controller
 * @category Dyode
 * @package  Dyode_Customerstatus
 * @module   Customerstatus
 * @author   Nithin
 */
class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Dyode\Customerstatus\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->order = $order;
        return parent::__construct($context);
    }

    public function execute()
    {
        //uncomment the below line and run the controller url for testing
        $order = $this->order->loadByIncrementId('000000006');
        $Customer_Status = $this->helper->checkCustomerStatus($order, '54421729');
    }
}
