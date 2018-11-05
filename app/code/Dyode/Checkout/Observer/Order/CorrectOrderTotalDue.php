<?php

namespace Dyode\Checkout\Observer\Order;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CorrectOrderTotalDue implements ObserverInterface
{

    /**
     * Correcting Total Due and Total Paid.
     *
     * We are changing these totals in order to change the authorize.net capture amount in the case of curacao
     * credit involved order. So we are putting it back here.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getPayment();
        $order = $payment->getOrder();

        $order->setTotalDue($order->getGrandTotal() - $order->getTotalDue());
        $order->setBaseTotalDue($order->getBaseGrandTotal() - $order->getBaseTotalDue());
        $order->setTotalPaid($order->getGrandTotal() - $order->getTotalPaid());
        $order->setBaseTotalPaid($order->getBaseGrandTotal() - $order->getBaseTotalPaid());

        return $this;
    }
}
