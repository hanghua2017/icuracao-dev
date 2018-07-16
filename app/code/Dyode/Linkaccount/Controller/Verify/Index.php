<?php


namespace Dyode\Linkaccount\Controller\Verify;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    protected $resultPageFactory;
    protected $customerSession;
    protected $helper;
    protected $messageManager;
    protected $customerModel;
    protected $_coreSession;
    protected $storeManager;
    protected $customerResourceFactory;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dyode\ARWebservice\Helper\Data $helper,
     * @param \Magento\Customer\Model\Session $customerSession,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customerModel,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->_coreSession = $coreSession;
        $this->messageManager = $messageManager;
        $this->customerModel = $customerModel;
        $this->customerResourceFactory = $customerResourceFactory;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postVariables = (array) $this->getRequest()->getPost();
        if(!empty($postVariables)){
            print_r($postVariables);
            $websiteId = $this->storeManager->getStore()->getWebsiteId();;
            $accountNumber = $this->_coreSession->getCurAcc();
            $custEmail  = $this->_coreSession->getCustEmail();
          /*$zipCode  = $postVariables['link_zipcode'];
            $dob = $postVariables['calendar_inputField'];
            $ssnLast = $postVariables['ssn-verify'];
            $maidenName = $postVariables['link_maiden']; */

            $accountNumber = '53752001';
            $zipCode  = '90015';
            $dob = '08-16-1971';
            $ssnLast = '5559';
            $maidenName = 'consumer';
            $postData = array(
                'CustID' => $accountNumber,
                'Zip' => $zipCode,
                'DOB' => $dob,
                'SSN' => $ssnLast,
                'MMaiden' => $maidenName,
                'Amount' => 1,
                'CCV' => ''
            );
            //Verify Credit Account Infm
            $accountInfo   =  $this->helper->verifyPersonalInfm($postData);
          //  print_r($accountInfo);
            $accountInfo = "OK";
            if($accountInfo == false){
                // Personal Infm failed
                $this->messageManager->addErrorMessage(__('Verification failed'));
                return $this;
            }
             $customer   = $this->customerResourceFactory->create();
             $customer->setWebsiteId($websiteId);

             // Preparing data for new customer
             //$customer->setEmail($email);
            // $customer->setFirstname($firstName);
          //   $customer->setLastname($lastName);
             $customer->setCuracaocustid($accountNumber);

             try
             {
                 $customer->save();
                 $this->customerSession->setCustomerAsLoggedIn($customer);
                 //$customerNew = $customerFactory->load($this->customerSession->getCustomer()->getId());
                 $customerNew = $this->customerModel->load($this->customerSession->getCustomer()->getId());
                 $this->_redirect('linkaccount/verify/success/');
                 $this->messageManager->addSuccess(__('Successfully verified'));
                 $resultPage = $this->resultPageFactory->create();
                 return $resultPage;
             } catch(\Exception $e) {
                 $errorMessage = $e->getMessage();
                 $this->_messageManager->addError($errorMessage);
                 $this->_redirect('linkaccount/verify/index');
             }

             $resultPage = $this->resultPageFactory->create();
             return $resultPage;
           }

           return $this->resultPageFactory->create();
    }

    /*
    * Function to save the customer details
    */
    public function saveCustomerDetails(){

    }

}
