<?php

namespace Dyode\InventoryUpdate\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Dyode\InventoryUpdate\Model\Inventory $inventory)
	{
		$this->inventory = $inventory;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$this->inventory->updateInventory();
		var_dump("success");exit;
	}

}
