<?php
namespace Dyode\ProcessEstimate\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Dyode\ProcessEstimate\Model\Estimate $estimate)
	{
		$this->estimate = $estimate;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$orders = $this->estimate->getOrders();
		var_dump($orders);exit;
	}

}
