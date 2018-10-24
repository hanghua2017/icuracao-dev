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
    protected $_customerModel;
    protected $_storeManager;
    protected $_customerRepositoryInterface;
    protected $_addressFactory;
    protected $_resultFactory;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /** @var \Magento\Framework\UrlFactory */
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
        \Magento\Customer\Model\Customer $customerModel,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        UrlFactory $urlFactory,
        CustomerFactory $customerFactory
    ) {
        
        $this->_resultFactory = $resultFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_customerModel = $customerModel;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_addressFactory = $addressFactory;
        $this->urlModel = $urlFactory->create(); 
        $this->quoteRepository = $quoteRepository;
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
            $customerId ='';
            $customerInfo  = $this->_customerSession->getCuracaoInfo();

            //Get Customer Id
            if($this->_customerSession->isLoggedIn()){
                $customerId    = $this->_customerSession->getCustomer()->getId();
            }
          
            $zipCode  = trim($postVariables['link_zipcode']);
            $curacaoCustId = trim($customerInfo->getAccountNumber());
            $dob = trim($postVariables['calendar_inputField']);
            $ssnLast = trim($postVariables['ssn-verify']);
            $maidenName = trim($postVariables['link_maiden']);

            
            $postData = array(
                'cust_id' => $curacaoCustId,
                'dob'=>$dob,
                'amount' => 1,
                'ssn'=>$ssnLast,
                'zip'=> $zipCode,
                'mmaiden'=>$maidenName
            );
            
            //Verify Credit Account Infm
            $accountInfo   =  $this->_helper->verifyPersonalInfm($postData);
            $accountInfo = true;
            if($accountInfo == false){
                // Personal Infm failed
                //$this->_messageManager->addErrorMessage(__('Verification failed [SSN /ZIP]'));
                $resultRedirect->setPath('linkaccount/verify/index');
                return $resultRedirect;
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
                }
                catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                    $defaultUrl = $this->urlModel->getUrl('customer/account/create/', ['_secure' => true]);
    
                }
                $customerId = $customer->getId();
             }
           
             $zipCode  = str_replace('-','',$customerInfo->getZipCode());//clearn up zip code

             //assign what region the state is in
             switch($customerInfo->getState())
             {
                     case 'AZ' : $reg_id = 4; break;
                     case 'CA' : $reg_id = 12; break;
                     case 'NV' : $reg_id = 39; break;
                     default   : $reg_id = 12; break;
             }
             //safe information t an array
             $_custom_address = array(
                                    'firstname' => $customerInfo->getFirstName(),
                                    'lastname' => $customerInfo->getFirstName(),
                                    'street' => array('0' => $customerInfo->getStreet(), '1' => '',),
                                    'city' => $customerInfo->getCity(),
                                    'region_id' => $reg_id,
                                    'postcode' => $zipCode,
                                    'country_id' => 'US',
                                    'telephone' => $customerInfo->getTelephone()
                                );

                           
            //get the customer address model and update the address information
            if($customerId){
                $customAddress = $this->_addressFactory->create();
                $customAddress  ->setData($_custom_address)
                                ->setCustomerId($customerId)
                                ->setIsDefaultBilling('1')
                                ->setIsDefaultShipping('1')
                                ->setSaveInAddressBook('1');
   
                try{
                      $customAddress->save();
                    //  $customer->setAddress($customAddress);
                    //  $customer->save();
                     
                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]); 
                    return $resultRedirect->setUrl($defaultUrl);
                }
                catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
       
                }
            }
            

          }
       return $this->_resultPageFactory->create();
    }


}
