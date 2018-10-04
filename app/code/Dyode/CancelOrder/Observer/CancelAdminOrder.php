<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\CancelOrder\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Dyode\CancelOrder\Helper\Data;
use \Magento\Framework\Event\Manager;

class CancelAdminOrder implements ObserverInterface
{
	/**
	 * @var \Dyode\CancelOrder\Helper\Data $cancelOrderHelper
	 */
	protected $_cancelOrderHelper;

	/**
	 * Construct
	 *
	 * @param \Dyode\CancelOrder\Helper\Data $cancelOrderHelper
	 */
	public function __construct(
		\Dyode\CancelOrder\Helper\Data $cancelOrderHelper
	) {
		$this->_cancelOrderHelper = $cancelOrderHelper;
	}

	/**
	 * Cancel Admin Order Action
	 *
	 * @param Observer $observer
	 */
	public function execute(Observer $observer)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . "/var/log/ordercancellation.log");
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);

		$order = $observer->getEvent()->getOrder();
		$orderId = $order->getIncrementId();
		// Getting the Invoice Number
		$invoiceNumber = $order->getData('estimatenumber');

		if (empty($invoiceNumber)) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("Invoice Number Not found ");
			throw new Exception("Invoice Number Not found ");
		}
		$response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);

		if (empty($response)) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("Order Item Id : " . $orderItem->getId());
			$logger->info("API Response not Found.");
			throw new Exception("API Response not Found", 1);
		}

		if ($response->OK != true) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("Order Item Id : " . $orderItem->getId());
			$logger->info($response->INFO);
			throw new \Exception($response->INFO);
		}
	}
}