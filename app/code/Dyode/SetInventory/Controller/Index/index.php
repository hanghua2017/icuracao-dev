<?php
namespace Dyode\SetInventory\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Dyode\SetInventory\Model\Update $update)
	{
		$this->update = $update;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$this->update->updateInventory();
		var_dump("success");exit;
	}

}
