<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ArInvoice\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Dyode\ArInvoice\Model\ArInvoice $arInvoice
     **/
    protected $arInvoice;

    /**
     * @var \Dyode\ArInvoice\Helper\Data $_arInvoiceHelper 
     **/
    protected $_arInvoiceHelper;

    /**
     * @var \Magento\Sales\Model\OrderRepository $orderRepository
     **/
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Model\ArInvoice $arInvoice,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
    ) {
        $this->arInvoice = $arInvoice;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = isset($_GET['id']) ? $_GET['id'] : null;
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/generateinvoicecron.log");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Cron Works");

        $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        $collection->addFieldToFilter('status', 'pending');

        if (!empty($orderId)) {
            $order = $this->_orderRepository->get($orderId);

            if ($order->getStatus() == "pending") {
                $this->arInvoice->createInvoice($orderId);
                $this->_arInvoiceHelper->linkAppleCare($order);
                echo "inventory update process ended"; die;
            } else {
                echo "Cannot process request now. Order is not Pending";
            }
        } else {
            foreach ($collection as $salesOrder) {
                $this->arInvoice->createInvoice($salesOrder->getId());
                $this->_arInvoiceHelper->linkAppleCare($salesOrder);
            }
        }

        echo "inventory update process ended"; die;
    }
}