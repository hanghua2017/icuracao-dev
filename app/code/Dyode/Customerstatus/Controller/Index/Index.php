<?php
namespace Dyode\Customerstatus\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Dyode\Customerstatus\Helper\Data $helper)
	{
		$this->helper = $helper;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$customerId = '53670063';
		$Customer_Status = $this->helper->checkCustomerStatus($customerId);
	}

}
