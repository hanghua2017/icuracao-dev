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
use Magento\Framework\DataObject;

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
     * @var Session
     */
    protected $customerSession;

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
        Session $customerSession
    )
    {
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->coreSession = $coreSession;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $registerPost,
        \Closure $proceed
    )
    {

        /** @var \Magento\Framework\App\RequestInterface $request */
        $checked = $registerPost->getRequest()->getParam('account-check');
        $curacaoId = $registerPost->getRequest()->getParam('curacaocustid');
      
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

                if($accountInfo == false){
                    // Personal Infm failed
                    $this->messageManager->addErrorMessage('Please enter correct Curacao Id');
                    $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
               
                    return $resultRedirect->setUrl($defaultUrl);
                }
                
                $this->setCuracaoSessionDetails($accountInfo, $registerPost);
                      
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
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

    public function setCuracaoSessionDetails($accountInfo,$registerPost)
    {
        $accountNumber = $registerPost->getRequest()->getParam('curacaocustid',false);
        $customerEmail = $registerPost->getRequest()->getParam('email', false);
        $password      = $registerPost->getRequest()->getParam('password', false);
        
        $curacaoInfo = new DataObject([
            'account_number' => $accountNumber,
            'email_address'  => $customerEmail,
            'first_name'     => $accountInfo->F_NAME,
            'last_name'      => $accountInfo->L_NAME,
            'street'         => $accountInfo->STREET,
            'city'           => $accountInfo->CITY,
            'state'          => $accountInfo->STATE,
            'zip_code'       => $accountInfo->ZIP,
            'previous_page'  => 'create',
            'password'       => $password,
            'telephone'      => $accountInfo->PHONE
        ]);

        $this->customerSession->setCuracaoInfo($curacaoInfo);

        return $this;
    }
}