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
		$writer = new \Zend\Log\Writer\Stream(BP . "/var/log/mylogfile.log");
        $logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);

		$order = $observer->getEvent()->getOrder();
		$orderId = $order->getIncrementId();
		$invoiceNumber = "ZEP58QX";

		$response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);

		if ($response->OK == true) {
			# code...
			$logger->info($response->OK);
		}
		else {
			# code...
			$logger->info($response->INFO);
			throw new \Exception($response->INFO);
		}
	}
}