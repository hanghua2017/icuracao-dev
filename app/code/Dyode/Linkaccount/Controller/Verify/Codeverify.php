<?php
/**
 * Dyode_Linkaccount Magento2 Module.
 *
 * Extending Magento_Customer
 *
 * @package   Dyode
 * @module    Dyode_Linkaccount
 * @author    kavitha@dyode.com
 */

namespace Dyode\Linkaccount\Controller\Verify;

use Dyode\ARWebservice\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\CustomerFactory;

class Codeverify extends Action
{

    protected $_resultPageFactory;
    /**
     * @var Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Dyode\ARWebservice\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $_addressFactory;

     /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /*
    * @var \Magento\Framework\UrlFactory
    */
    protected $urlModel;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        AddressFactory $addressFactory,
        ResultFactory $resultFactory,
        UrlFactory $urlFactory,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->resultFactory = $resultFactory;
        $this->_addressFactory = $addressFactory;
        $this->urlModel = $urlFactory->create();
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = '';
        $attempts = 0;
        $postVariables = (array) $this->getRequest()->getPost();

        if (!empty($postVariables)) {

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $customerInfo  = $this->customerSession->getCuracaoInfo();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $curacaoCustId = trim($customerInfo->getAccountNumber());

            if ( !( $customerInfo->getEmailAddress() ) && !( $curacaoCustId ) ) {
                $this->messageManager->addErrorMessage( "Please enter Email address and Curacao Customer Number " );
                $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setUrl($defaultUrl);
            }

            $verificationCode = trim ( $postVariables['verification_code'] );
            $encodeCode = $this->customerSession->getEncCode();
            $this->customerSession->unsEncCode();
            if (isset($verificationCode)) {
                // Check if code is good 0 good -1 no good
                $checkResult =   $this->helper->verifyCode($encodeCode, $verificationCode);

                 //If Result is verified then link the Curacao account with Magento
                 if ($checkResult == 0) {

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
                         $this->customerSession->setCustomerAsLoggedIn($customer);
                         $this->customerSession->setCurAcc($curacaoCustId);
                         $this->customerSession->setFname($customerInfo->getFirstName());
                         $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]);
                        
                         return $resultRedirect->setUrl($defaultUrl);
                        }
                     catch(\Exception $e) {
                         $errorMessage = $e->getMessage();
                         $this->messageManager->addErrorMessage($errorMessage);
                         $defaultUrl = $this->urlModel->getUrl('customer/account/create/', ['_secure' => true]);
                     }
                    //  $customerId = $customer->getId();
                    //  $zipCode  = str_replace('-','',$customerInfo->getZipCode());//clearn up zip code
                    //  //assign what region the state is in
                    //  switch($customerInfo->getState()) {
                    //      case 'AZ' : $reg_id = 4; break;
                    //      case 'CA' : $reg_id = 12; break;
                    //      case 'NV' : $reg_id = 39; break;
                    //      default   : $reg_id = 12; break;
                    //  }
                    //  //safe information t an array
                    //  $_custom_address = array(
                    //      'firstname' => $customerInfo->getFirstName(),
                    //      'lastname' => $customerInfo->getFirstName(),
                    //      'street' => array('0' => $customerInfo->getStreet(), '1' => '',),
                    //      'city' => $customerInfo->getCity(),
                    //      'region_id' => $reg_id,
                    //      'postcode' => $zipCode,
                    //      'country_id' => 'US',
                    //      'telephone' => $customerInfo->getTelephone()
                    //  );
                    //  //get the customer address model and update the address information
                    //  if($customerId){
                    //      $customAddress = $this->_addressFactory->create();
                    //      $customAddress  ->setData($_custom_address)
                    //          ->setCustomerId($customerId)
                    //          ->setIsDefaultBilling('1')
                    //          ->setIsDefaultShipping('1')
                    //          ->setSaveInAddressBook('1');
                    //      try{
                    //          $customAddress->save();

                    //          $defaultUrl = $this->urlModel->getUrl('linkaccount/verify/success', ['_secure' => true]);
                    //          return $resultRedirect->setUrl($defaultUrl);
                    //      } catch(\Exception $e) {
                    //          $errorMessage = $e->getMessage();
                    //          $this->messageManager->addErrorMessage($errorMessage);
                    //          $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);

                    //      }
                    // }

                   

                } else {
                     //Crossed 5 attempts
                    $this->messageManager->addErrorMessage(
                        'Verification code is wrong'
                    );
                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                    return $resultRedirect->setUrl($defaultUrl);
                }

            } else{

                $this->messageManager->addErrorMessage( "Please enter your verification code " );
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setUrl($defaultUrl);
            }
        }
    }
}
