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
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\CustomerFactory;

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
    protected $_quote;
    protected $_checkoutSession;
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
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        UrlFactory $urlFactory,
        CustomerFactory $customerFactory
    ) {
        
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
        $this->urlModel = $urlFactory->create(); 
        $this->_quote = $quote;
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
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
            $this->_coreSession->start();
            $downPayment = 0;
            $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            $curacaoCustId = $this->_coreSession->getCurAcc();
            $custEmail  = $this->_coreSession->getCustEmail();
            $customerInfo  = $this->_coreSession->getCustomerInfo();
            $password = $this->_coreSession->getPass();
            $customerId = '';

            //Get Customer Id
            if($this->_customerSession->isLoggedIn()){
                $customerId    = $this->_customerSession->getCustomer()->getId();
            }
          
            $zipCode  = $postVariables['link_zipcode'];
            $customerInfo  = $this->_coreSession->getCustomerInfo();
            $dob = $postVariables['calendar_inputField'];
            $ssnLast = $postVariables['ssn-verify'];
            $maidenName = $postVariables['link_maiden'];

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cart   = $objectManager->get('\Magento\Checkout\Model\Cart');
            $totals = $cart->getQuote()->getTotals();
            $subtotal = $totals['subtotal']['value'];           
            if($subtotal){
              $amount =   $subtotal;
            } else{
              $amount = '30.00';
            }
            $postData = array(
                'cust_id' => $curacaoCustId,
                'amount' => $amount,
                'ssn'=>$ssnLast,
                'zip'=> $zipCode
            );

            //Verify Credit Account Infm
            $accountInfo   =  $this->_helper->verifyPersonalInfm($postData);

            if($accountInfo == false){
                // Personal Infm failed
                $this->_messageManager->addErrorMessage(__('Verification failed'));
                $resultRedirect->setPath('linkaccount/verify/index');
                return $resultRedirect;
            }

             $downPayment = $accountInfo->DOWNPAYMENT;
             //Linking the account
             if ($customerId) {
               $customer = $this->_customerRepositoryInterface->getById($customerId);
               $customer->setCustomAttribute('curacaocustid', $curacaoCustId);
               $this->_customerRepositoryInterface->save($customer);
             } else {
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
            if($customerId){
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
                      $this->_customerSession->setDownPayment($downpayment);
                     
                        if(isset($path)){
                           $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]);       
                        } else{
                            $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);
                        }
                        return $resultRedirect->setUrl($defaultUrl);
                }
                catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $this->messageManager->addErrorMessage('Please enter correct Details'.$errorMessage);
                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
       
                }
            }
            

          }
       return $this->_resultPageFactory->create();
    }


}
