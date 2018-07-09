<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

// use Magento\Framework\View\Element\Template\Context;
use Dyode\ArInvoice\Helper\Data;
// use Magento\Framework\Model\Context;
// use Magento\Framework\Registry;

class OrderCollection extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{   
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     **/
    protected $orderCollectionFactory;

    protected $_pageFactory;
    
    protected $data;
    
    protected $_helper;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Dyode\ArInvoice\Helper\Data $helper,
        \Magento\Framework\Registry $data
    ) {
		$this->helper = $helper;
        $this->_pageFactory = $pageFactory;
        $this->_helper = $helper;
		return parent::__construct($context, $data);
	}

    public function getSalesOrderCollection()
    {       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        $orderCollection->load();
        // $helperFactory = $objectManager->get('\Magento\Core\Model\Factory\Helper');
        // print_r($orderCollection->getData());
        foreach ($orderCollection as $order) {
            $paymentMethod = $order->getPayment()->getMethod();
            // // $methodTitle = $method->getTitle();
            // print_r($order->getData());
            // print_r($order->getPayment()->getMethod());
            // print_r($methodTitle);
            // echo " ";
            if (strpos($paymentMethod, 'authorizenet') !== false) {
                echo 'Authorize.net';
            } else {
                echo 'false';
            }
            echo " ";
            if (!empty($order->getCustomerId())) {
                echo $this->_helper->validateAccountNumber($order->getCustomerId());
                // var_dump($order->getCustomerId());    
            } else {
                echo "Account Number not found!";
            }
        }
        die();
        // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        // // $this->orderCollectionFactory->addFieldToFilter('status','complete');
        // return $collection;
    }
    
}
