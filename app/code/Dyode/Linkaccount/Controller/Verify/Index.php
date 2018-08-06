<?php
/*
Date: 12/07/2018
Author :Kavitha
*/

namespace Dyode\Linkaccount\Controller\Verify;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    protected $_resultPageFactory;
    protected $_customerSession;
    protected $_helper;
    protected $_messageManager;
    protected $_customerModel;
    protected $_coreSession;
    protected $_storeManager;
    protected $_customerResourceFactory;
    protected $_customerRepositoryInterface;
    protected $_addressFactory;
    protected $_resultFactory;
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
        ResultFactory $resultFactory,
        \Magento\Customer\Model\Customer $customerModel,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory
    ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_coreSession = $coreSession;
        $this->_messageManager = $messageManager;
        $this->_customerModel = $customerModel;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerResourceFactory = $customerResourceFactory;
        $this->_addressFactory = $addressFactory;
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
            $this->_coreSession->start();
            $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();;
            $accountNumber = $this->_coreSession->getCurAcc();
            $custEmail  = $this->_coreSession->getCustEmail();
            $customerInfo  = $this->_coreSession->getCustomerInfo();
            //Get Customer Id
            $customerId    = $this->_customerSession->getCustomer()->getId();
            $zipCode  = $postVariables['link_zipcode'];
            $customerInfo  = $this->_coreSession->getCustomerInfo();
            $dob = $postVariables['calendar_inputField'];
            $ssnLast = $postVariables['ssn-verify'];
            $maidenName = $postVariables['link_maiden'];

            $postData = array(
                'cust_id' => $accountNumber,
                'amount' => 1,

            );

            //Verify Credit Account Infm
            $accountInfo   =  $this->_helper->verifyPersonalInfm($postData);

            if($accountInfo == 0 || $accountInfo ==''){
                // Personal Infm failed
                $this->_messageManager->addErrorMessage(__('Verification failed'));
                $resultRedirect->setPath('linkaccount/verify/index');
                return $resultRedirect;
            }

             //Linking the account
             if ($customerId) {
               $customer = $this->_customerRepositoryInterface->getById($customerId);
               $customer->setCustomAttribute('curacaocustid', $accountNumber);
               $this->_customerRepositoryInterface->save($customer);
             }

             $customerInfo["ZIP"] = str_replace('-','',$customerInfo['ZIP']);//clearn up zip code

             //assign what region the state is in
             switch($customerInfo['STATE'])
             {
                     case 'AZ' : $reg_id = 4; break;
                     case 'CA' : $reg_id = 12; break;
                     case 'NV' : $reg_id = 39; break;
                     default   : $reg_id = 12; break;
             }
             //safe information t an array
             $_custom_address = array('firstname' => $customerInfo['F_NAME'],
                             'lastname' => $customerInfo['L_NAME'],
                             'street' => array('0' => $customerInfo['STREET'], '1' => '',),
                             'city' => $customerInfo['CITY'],
                             'region_id' => $reg_id,
                             'postcode' => $customerInfo['ZIP'],
                             'country_id' => 'US',
                             'telephone' => $customerInfo['PHONE']);


             //get the customer address model and update the address information

             $customAddress = $this->_addressFactory->create();
             $customAddress  ->setData($_custom_address)
                             ->setCustomerId($customerId)
                             ->setIsDefaultBilling('1')
                             ->setIsDefaultShipping('1')
                             ->setSaveInAddressBook('1');

             try{
                   $customAddress->save();
                   $customer->setAddress($customAddress);
                   $this->_customerSession->setCustomerAsLoggedIn($customer);
                   //$this->_redirect('linkaccount/verify/success');
                   $resultRedirect->setPath('linkaccount/verify/success');
                   return $resultRedirect;
             }
             catch(\Exception $e) {
                 $errorMessage = $e->getMessage();
                 $this->_messageManager->addError($errorMessage);
                 $this->_redirect('linkaccount/verify/index');
             }

          }
       return $this->_resultPageFactory->create();
    }


}
