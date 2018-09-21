<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       17/09/2018
 */
 
namespace Dyode\Linkaccount\Model;

use \Dyode\Linkaccount\Api\LinkAccountInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Model\Context;

class LinkAccount extends \Magento\Framework\Model\AbstractModel implements LinkAccountInterface
{

      protected $_customerModel;
      protected $_storeManager;
      protected $_customerResourceFactory;
      protected $_customerRepositoryInterface;
      protected $_accountManagmentInterface;
      /**
       * @var \Magento\Quote\Api\CartRepositoryInterface
       */
      protected $quoteRepository;
      /**
       * Constructor
       *
       * @param \Magento\Framework\Model\Context  $context
       * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
       * @param \Dyode\ARWebservice\Helper\Data $helper,
       * @param \Magento\Customer\Model\Session $customerSession,
       * @param \Magento\Framework\Message\ManagerInterface $messageManager
       */
      public function __construct(
          Context $context,
          \Magento\Customer\Model\Customer $customerModel,
          \Magento\Store\Model\StoreManagerInterface $storeManager,
          \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
          \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
          AccountManagementInterface $accountManagmentInterface,
          \Magento\Framework\Registry $registry

      ) {
          parent::__construct($context, $registry);
          $this->_storeManager = $storeManager;
          $this->_customerModel = $customerModel;
          $this->_customerRepositoryInterface = $customerRepositoryInterface;
          $this->_customerResourceFactory = $customerResourceFactory;
          $this->_accountManagmentInterface = $accountManagmentInterface;
        }


        public function create($curacaoid,$email,$fname,$lname,$password=null){
            if(($curacaoid) && ($email) && ($fname) && ($lname)){
              if($password == null){
                // check email already AlreadyExists
                $emailAvailable = $this->_accountManagmentInterface->isEmailAvailable($email);
                if($emailAvailable){
                    //Create a password and save the customer
                    // Get Website ID
                    $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

                    // Instantiate object (this is the most important part)
                    $customer = $this->_customerResourceFactory->create();

                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail("test@mail.com");
                    $customer->setFirstname("First Name");
                    $customer->setLastname("Last name");
                    $customer->setPassword("pass@123");
                  //  $customer->setCura
                  //  $customer->setPrefix("Herr");

                    $customer->setAddresses(null);

                    $storeId = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
                    $customer->setStoreId($storeId);

                    $storeName = $this->storeManager->getStore($customer->getStoreId())->getName();
                    $customer->setCreatedIn($storeName);

                    /*
                     * TODO
                     * Problem with custom attributes
                     */

                    $customerData = $customer->getDataModel();
                    $customerData->setCustomAttribute("curacaocustid", "123456");
                    $customer->updateData($customerData);

                    $customer->save();
                    $customer->sendNewAccountEmail();

                }
              }
            }
        }

}
