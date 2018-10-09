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

namespace Dyode\Linkaccount\Model\Plugin\Controller\Account;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use \Magento\Framework\Session\SessionManagerInterface as CoreSession; 
use Dyode\ARWebservice\Helper\Data;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface; 
use Magento\Customer\Model\Session;

class VerifyCuracaoId
{

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**@var \Magento\Framework\Session\SessionManagerInterface */
    protected $coreSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**@var \Dyode\ARWebservice\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * RestrictCustomerEmail constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param Session $customerSession
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        CoreSession $coreSession,
        Data $helper,
        CustomerFactory $customerFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager
    )
    {
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->coreSession = $coreSession;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->session = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed
    )
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $email = $subject->getRequest()->getParam('email');
        $fName = $subject->getRequest()->getParam('firstname');
        $lName = $subject->getRequest()->getParam('lastname');
        $checked = $subject->getRequest()->getParam('account-check');
        $curacaoId = $subject->getRequest()->getParam('curacaocustid');
        $password =  $subject->getRequest()->getParam('password');
        //echo $curacaoId .$checked;
        if(!empty($curacaoId) && ($checked)){
            $this->coreSession->start();
            $resultRedirect = $this->resultRedirectFactory->create();

            $attempts  =  $this->coreSession->getAttempts();
            if($attempts == 10){
                $this->coreSession->unsAttempts();    
            }
            if($attempts == '' ){
                $attempts = 1;
                $this->coreSession->setAttempts($attempts);
            }
            if($attempts < 10){
                $attempts +=1;
                $this->coreSession->setAttempts($attempts);   
               
                //Verify Credit Account Infm
                $accountInfo   =  $this->helper->getARCustomerInfoAction($curacaoId);
                
                if($accountInfo !== false){
                    // Get Website ID
                    $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
                    $this->coreSession->setCurAcc($curacaoId);
                    $this->coreSession->setCustEmail($email);
                    $this->coreSession->setCustomerInfo($accountInfo);  
                    $this->coreSession->setPass($password);  
                    $this->coreSession->setFname($fName);  
                    $this->coreSession->setLastname($lName);  
                    $this->coreSession->setPath("linkaccount/verify/success");

                    $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        return $resultRedirect->setUrl($defaultUrl);
                }
                $this->messageManager->addErrorMessage('Please enter correct Curacao Id');
                $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
               
                return $resultRedirect->setUrl($defaultUrl);

            } else {
                //Crossed 5 attempts
                $this->messageManager->addErrorMessage(
                    'You have crossed 5 attempts !!'
                );
                $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setUrl($defaultUrl);
            }
        
        
        }
   
     return $proceed();
    }
}