<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

class OrderCollection extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{   
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     **/
    protected $orderCollectionFactory;

    public function getSalesOrderCollection()
    {       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        $orderCollection->load();
        // print_r($orderCollection->getData());
        foreach ($orderCollection as $order) {
            $paymentMethod = $order->getPayment()->getMethod();
        //     // // $methodTitle = $method->getTitle();
        //     print_r($order->getData());
        //     // print_r($order->getPayment()->getMethod());
        //     // print_r($methodTitle);
        //     // echo " ";
            if (strpos($paymentMethod, 'authorizenet') !== false) {
                echo 'Authorize.net';
            } else {
                echo 'false';
            }
            echo " ";
        }
        die();
        // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        // // $this->orderCollectionFactory->addFieldToFilter('status','complete');
        // return $collection;
    }     
}
