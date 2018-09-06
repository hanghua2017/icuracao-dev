<?php

namespace Dyode\Threshold\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Dyode\Threshold\Model\Threshold $thresholdModel)
	{
		$this->thresholdModel = $thresholdModel;
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$this->thresholdModel->getThreshold();
	}

}

