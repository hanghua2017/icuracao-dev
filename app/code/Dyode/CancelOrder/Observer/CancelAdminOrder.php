<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
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
			$logger->info("Estimate Number not found" . "| Order Id: " . $order->getIncrementId());
			throw new \Exception("Estimate Number not found");
		}
		$response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);

		if ($response->OK != true) {
			$logger->info($response->INFO . " Order Id: " . $order->getIncrementId());
			throw new \Exception($response->INFO);
		}
	}
}