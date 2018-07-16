<?php


namespace Dyode\Linkaccount\Controller\Credit;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    protected $resultPageFactory;
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ResultFactory $resultFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
      $postVariables = (array) $this->getRequest()->getPost();
      $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
      if(!empty($postVariables)){
        //get the customer //
        $custEmail  = $this->_coreSession->getCustEmail();
        $customerId = $this->customerSession->getCustomer()->getId();
        if(!$customerId)
        {
          $resultRedirect->setPath('linkaccount/index');
          return $resultRedirect;
        }
        $accountNumber = $postVariables['curacao_account'];

      }
      return $this->resultPageFactory->create();

    }

}
