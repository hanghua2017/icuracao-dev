<?php
/*
Date: 16/07/2018
Author :Kavitha
*/

namespace Dyode\Linkaccount\Controller\Verify;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\CustomerFactory;

class Codeverify extends Action
{

    protected $_resultPageFactory;
    protected $_customer;
    protected $_coreSession;
    protected $_helper;
    protected $_resultFactory;
    protected $_customerSession;
    protected $_customerRepositoryInterface;
    protected $_addressFactory;
    
     /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /*
    * @var \Magento\Framework\UrlFactory
    */
    protected $urlModel;

     /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        ResultFactory $resultFactory,
        UrlFactory $urlFactory,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customer = $customer;
        $this->_coreSession = $coreSession;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
        $this->_resultFactory = $resultFactory;
        $this->_customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_addressFactory = $addressFactory;
        $this->urlModel = $urlFactory->create(); 
        $this->customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId ='';
        $attempts = 0;
        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $curacaoCustId = $this->_coreSession->getCurAcc();
        $custEmail  = $this->_coreSession->getCustEmail();
        $customerInfo  = $this->_coreSession->getCustomerInfo();
        $password = $this->_coreSession->getPass();

        $postVariables = (array) $this->getRequest()->getPost();
        $this->_coreSession->start();

        $attempts  =  $this->_coreSession->getAttempts();
        if($attempts == 10){
            $this->_coreSession->unsAttempts();    
        }
        if($attempts == '' ){
            $attempts = 1;
            $this->_coreSession->setAttempts($attempts);
        }
        if($attempts < 10){
            $attempts +=1;
            $this->_coreSession->setAttempts($attempts);   
            if(!empty($postVariables)){
              //Get Customer Id
                if($this->_customerSession->isLoggedIn()){
                  $customerId    = $this->_customerSession->getCustomer()->getId();
                } 

                $accountNumber = $this->_coreSession->getCurAcc();
                $customerInfo  = $this->_coreSession->getCustomerInfo();
                $encodeCode = $this->_coreSession->getEncCode();

                $verification_code = $postVariables['verification_code'];
                $userinfo = array(
                        "cu_account"=> $accountNumber,
                        "verification_code"=> $verification_code
                );

                // Check if code is good 0 good -1 no good
                $checkResult =   $this->_helper->verifyCode(  $encodeCode, $verification_code );

                //If Result is verified then link the Curacao account with Magento
                if($checkResult == 0){
                    //Linking the account
                    if ($customerId) {
                      $customer = $this->_customerRepositoryInterface->getById($customerId);
                      $customer->setCustomAttribute('curacaocustid', $accountNumber);
                      $this->_customerRepositoryInterface->save($customer);
                    } else{
                      $fName = $this->_coreSession->getFname();                   
                      $lName = $this->_coreSession->getLastname();
                      $path = $this->_coreSession->getPath();
                      // Instantiate object (this is the most important part)
                      $customer = $this->customerFactory->create();
                      $customer->setWebsiteId($websiteId);
                      // Preparing data for new customer
                      $customer->setEmail($custEmail);
                      $customer->setFirstname($fName);
                      $customer->setLastname($lName);
                      $customer->setPassword($password);
                      // Save Curacao Customer Id
                      if (!empty($curacaoCustId)) {
                          $customerData = $customer->getDataModel();
                          $customerData->setCustomAttribute('curacaocustid', $curacaoCustId);
                          $customer->updateData($customerData);
                      }
                      // Save data
                      $customer->save();
                      $customerId = $customer->getId();
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
                            $customer->save();
                            $this->_customerSession->setCustomerAsLoggedIn($customer);
                            if(isset($path)){
                              $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]);       
                            } else{
                                $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);
                            }
                            return $resultRedirect->setUrl($defaultUrl);
                      }
                      catch (Exception $e) {
                        $errorMessage = $e->getMessage();
                         $this->messageManager->addErrorMessage('Code is wrong'.$errorMessage);
                        $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                      }
                }
            }           
        } else{
           //Crossed 5 attempts
            $this->messageManager->addErrorMessage(
                'You have crossed 5 attempts !!'
            );
            $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            return $resultRedirect->setUrl($defaultUrl);
        }
     }

}
