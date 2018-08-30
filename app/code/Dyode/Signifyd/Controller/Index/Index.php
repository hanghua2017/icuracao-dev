<?php

namespace Dyode\Signifyd\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Sales\Api\Data\OrderInterface $orderInterface,
		\Magento\Signifyd\Api\CaseManagementInterface $caseManagement,
		\Dyode\Signifyd\Helper\Data $helper)
	{
		$this->helper = $helper;
		$this->_pageFactory = $pageFactory;
		$this->orderInterface = $orderInterface;
		$this->caseManagement = $caseManagement;
		return parent::__construct($context);
	}

	public function execute()
    {
    	$order = $this->orderInterface->loadByIncrementId('000000008'); 
    	$orderIncrementId = $order->getEntityId();

    	$gurantee = $this->caseManagement->getByOrderId($orderIncrementId)->getGuaranteeDisposition();
    	var_dump($gurantee);
    	echo "custom module";
    }
}