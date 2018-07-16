<?php
namespace Dyode\Linkaccount\Controller\Verify;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Code extends \Magento\Framework\App\Action\Action {
    protected $resultJsonFactory;
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
       parent::__construct($context);
       $this->resultJsonFactory = $resultJsonFactory;
     }
   public function execute()
   {
    //  $id = $this->getRequest()->getParam('id', false);
      $resultData = array("hi");
      $resultJson = $this->resultJsonFactory->create();
      return $resultJson->setData($resultData);
   }
}
