<?php


namespace Dyode\Linkaccount\Controller\Credit;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    protected $_resultPageFactory;
    protected $_customerSession;
    protected $_customerRepositoryInterface;
    protected $_helper;
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
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_helper = $helper;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
      $postVariables = (array) $this->getRequest()->getPost();
      $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
      if(!empty($postVariables)){
        //get the customer //
        $custEmail  = $this->_coreSession->getCustEmail();
        $customerId = $this->_customerSession->getCustomer()->getId();
        if(!$customerId)
        {
          $resultRedirect->setPath('linkaccount/index');
          return $resultRedirect;
        }
        $accountNumber = $postVariables['curacao_account'];
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        if($customer){
            //Check customer already updated the curacao
            $curaAccId = $customer->getCuracaocustid();
            if($curaAccId != '' && $curaAccId == $accountNumber){
                //Already linked with the Magento Account
                $this->_messageManager->addError(__('We were unable to submit your request. Please try again!'));
                return $this->_redirect('linkaccount/index');
            }
            $this->_coreSession->setCurAcc($accountNumber);
            //Verify Credit Account Infm
            $accountInfo   =  $this->_helper->getARCustomerInfoAction($accountNumber);

            $this->_coreSession->setCustomerInfo($accountInfo);
            if($accountInfo !== false){
                $resultRedirect->setPath('linkaccount/verify/index');
                return $resultRedirect;
            }
          }


      }
      return $this->_resultPageFactory->create();

    }

}
