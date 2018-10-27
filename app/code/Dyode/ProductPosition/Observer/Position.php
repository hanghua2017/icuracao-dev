<?php
namespace Dyode\ProductPosition\Observer;

class Position implements \Magento\Framework\Event\ObserverInterface
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
     //$order= $observer->getData('order');
	 //$order->doSomething();
   var_dump("success");exit;

     return $this;
  }
}
