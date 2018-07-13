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
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
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
            print_r($accountInfo);
            if($accountInfo == false){
                // Personal Infm failed
                $this->messageManager->addErrorMessage(__('This is bad'));
                return $this;
            }
            //exit;           
        }
        return $this->resultPageFactory->create();              
    }

    /*
    * Function to save the customer details 
    */
    public function saveCustomerDetails(){

    }

}

