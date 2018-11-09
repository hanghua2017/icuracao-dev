<?php
/**
 * Dyode_ARWebservice Magento2 Module.
 *
 *
 * @package Dyode
 * @module  Dyode_ARWebservice
 * @author  Kavitha <kavitha@dyode.com>
 * @date    12/07/2018
 * @copyright Copyright © Dyode
 */
namespace Dyode\Linkaccount\Controller\Verify;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\CustomerFactory;

class Index extends Action
{

    protected $_resultPageFactory;
    protected $_customerSession;
    protected $_helper;
    protected $_messageManager;
  
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;
    /**
     * @var Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;
  
    /** 
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
     * @param \Dyode\ARWebservice\Helper\Data $helper,
     * @param \Magento\Customer\Model\Session $customerSession,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ResultFactory $resultFactory,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        UrlFactory $urlFactory,
        CustomerFactory $customerFactory
    ) {
        
        $this->_resultFactory = $resultFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_addressFactory = $addressFactory;
        $this->urlModel = $urlFactory->create(); 
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
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
          
            $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            $customerInfo  = $this->_customerSession->getCuracaoInfo();
            $customerId ='';
            

            if(empty($customerInfo) && !isset($customerInfo)){
                $this->messageManager->addErrorMessage('Please enter the Curacao Id');
                $defaultUrl = $this->urlModel->getUrl('customer/account/create/', ['_secure' => true]);
            }

            //Get Customer Id
            if($this->_customerSession->isLoggedIn()){
                $customerId    = $this->_customerSession->getCustomer()->getId();
            }
            
            if(isset($postVariables['calendar_inputField'])){
                $dob = date("Y-m-d", strtotime ($postVariables['calendar_inputField']) );
            }
            $zipCode  = trim($postVariables['link_zipcode']);
            $curacaoCustId = trim($customerInfo->getAccountNumber());
            $ssnLast = trim($postVariables['ssn-verify']);
            $maidenName = trim($postVariables['link_maiden']);
            $postData = array();
            $postData['cust_id']  =  $curacaoCustId;
            $postData['amount'] = 1;
            $verify = 0;

            if(isset($ssnLast)  && ($ssnLast != '')){
                $postData['ssn']  =  $ssnLast;
                $postData['dob']  =  $dob;
                $verify = 1;
            } 
            if(isset($maidenName) && ($maidenName != '') ){
                $postData['mmaiden']  =  $maidenName;
                if(isset($zipCode)){
                    $postData['zip']  =  $zipCode;
                    $verify =1;
                } else if(isset($dob)) {
                    $postData['dob']  =  $dob;
                    $verify = 1;
                }
            } 
            
            if($verify ==0 ){
                $this->_messageManager->addErrorMessage("Please enter SSN and DOB OR Mothers Maiden Name and Zipcode OR Mothers Maiden Name and DOB");
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                return $resultRedirect->setUrl($defaultUrl);
            }

            //Verify Credit Account Infm
            try{
                $accountInfo   =  $this->_helper->verifyPersonalInfm($postData);
            } 
            catch(\Dyode\ARWebservice\Exception\ArResponseException $e){
                $this->_messageManager->addErrorMessage($e->getMessage());
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                return $resultRedirect->setUrl($defaultUrl);
            }
            catch(Exception $e){
                $this->_messageManager->addErrorMessage($e->getMessage());
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                return $resultRedirect->setUrl($defaultUrl);
            }
         
            if ($customerId) {
               $customer = $this->_customerRepositoryInterface->getById($customerId);
               $customer->setCustomAttribute('curacaocustid', $curacaoCustId);
               $this->_customerRepositoryInterface->save($customer);
             } else {

                // Instantiate object (this is the most important part)
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                // Preparing data for new customer
                $customer->setEmail($customerInfo->getEmailAddress());
                $customer->setFirstname($customerInfo->getFirstName());
                $customer->setLastname($customerInfo->getLastName());
                $customer->setPassword($customerInfo->getPassword());

                // Save Curacao Customer Id
                if (!empty($curacaoCustId)) {
                    $customerData = $customer->getDataModel();
                    $customerData->setCustomAttribute('curacaocustid', $curacaoCustId);
                    $customer->updateData($customerData);
                }
                try{
                    // Save data
                    $customer->save();
                    $this->_customerSession->setCustomerAsLoggedIn($customer);   
                    $this->_customerSession->setCurAcc($curacaoCustId);                
                    $this->_customerSession->setFname($customerInfo->getFirstName());
                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]); 
                    
                    return $resultRedirect->setUrl($defaultUrl);
                }
                catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                    $defaultUrl = $this->urlModel->getUrl('customer/account/create/', ['_secure' => true]);
    
                }
                $customerId = $customer->getId();
             }
            //  $zipCode  = str_replace('-','',$customerInfo->getZipCode());//clearn up zip code

            //  //assign what region the state is in
            // switch($customerInfo->getState())
            // {
            //     case 'AZ' : $reg_id = 4; break;
            //     case 'CA' : $reg_id = 12; break;
            //     case 'NV' : $reg_id = 39; break;
            //     default   : $reg_id = 12; break;
            // }
            //  //safe information t an array
            // $_custom_address = array(
            //     'firstname' => $customerInfo->getFirstName(),
            //     'lastname' => $customerInfo->getFirstName(),
            //     'street' => array('0' => $customerInfo->getStreet(), '1' => '',),
            //     'city' => $customerInfo->getCity(),
            //     'region_id' => $reg_id,
            //     'postcode' => $zipCode,
            //     'country_id' => 'US'
            // );

            // //get the customer address model and update the address information
            // if($customerId){
            //     $customAddress = $this->_addressFactory->create();
            //     $customAddress->setData($_custom_address)
            //         ->setCustomerId($customerId)
            //         ->setIsDefaultBilling('1')
            //         ->setIsDefaultShipping('1')
            //         ->setSaveInAddressBook('1');
   
            //     try{
            //         $customAddress->save();
            //         //  $customer->setAddress($customAddress);
            //         //  $customer->save();
                     
            //         $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]); 
            //         return $resultRedirect->setUrl($defaultUrl);
            //     }
            //     catch(\Exception $e) {
            //         $errorMessage = $e->getMessage();
            //         $this->messageManager->addErrorMessage($errorMessage);
            //         $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
            //         return $resultRedirect->setUrl($defaultUrl);
            //     }
            // }
            

          }
        return $this->_resultPageFactory->create();
    }
}
