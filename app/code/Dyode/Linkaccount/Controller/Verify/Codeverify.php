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
        ResultFactory $resultFactory
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
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $attempts = 0;
        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $postVariables = (array) $this->getRequest()->getPost();
        $this->_coreSession->start();

        $attempts  =  $this->_coreSession->getAttempts();
        if($attempts == '')
          $attempts = 1;

        $this->_coreSession->setAttempts($attempts);
        if($attempts > 5){
          //Attempts crossed 5
          $this->_messageManager->addError(__('You have crossed 5 attempts!'));
          return $this->_redirect('linkaccount/index');
        } else{
          $attempts +=1;
        }
        if(!empty($postVariables)){
            //Get Customer Id
            $customerId    = $this->_customerSession->getCustomer()->getId();
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
                        $this->_redirect('linkaccount/verify/success');
                  }
                  catch (Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->_messageManager->addError($errorMessage);
                    $this->_redirect('linkaccount/verify/index');
                  }
            }
        }
        $this->_redirect('linkaccount/verify/index');
     }

}
