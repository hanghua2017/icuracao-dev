<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/08/2018
 */

namespace Dyode\Checkout\Controller\Credit;
use Magento\Framework\App\Action\Action;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action {

  protected $_resultPageFactory;
  protected $_customerSession;
  protected $_helper;
  protected $_checkoutSession;
  protected $_coreSession;
  protected $_messageManager;
  protected $_resultFactory;
  /**
   * Constructor
   *
   * @param \Magento\Framework\App\Action\Context  $context
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   */
  public function __construct(
      Context $context,
      PageFactory $resultPageFactory,
      ResultFactory $resultFactory,
      \Dyode\ARWebservice\Helper\Data $helper,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Framework\Session\SessionManagerInterface $coreSession,
      \Magento\Framework\Message\ManagerInterface $messageManager,
      \Magento\Customer\Model\Session $customerSession
  ) {
      parent::__construct($context);
      $this->_resultFactory = $resultFactory;
      $this->_resultPageFactory = $resultPageFactory;
      $this->_customerSession = $customerSession;
      $this->_helper = $helper;
      $this->_coreSession = $coreSession;
      $this->_messageManager = $messageManager;
      $this->_checkoutSession = $checkoutSession;
  }
  /**
   * Execute view action
   *
   * @return \Magento\Framework\Controller\ResultInterface
   */
  public function execute()
  {
  //stdClass Object ( [CELL] => (323)274-6810 [CITY] => LOS ANGELES [CUST_ID] => 52398671 [F_NAME] => MARTHA [L_NAME] => LINARES [OTHER] => AURELIA [PHONE] => (323)274-6810 [STATE] => CA [STREET] => 846 W 42ND PL APT 2 [ZIP] => 90037 )
    $postVariables = (array) $this->getRequest()->getParams();
    $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
    if(!empty($postVariables)){
       
        $accountNumber = $postVariables['curacao_account'];
        $customerEmail = $postVariables['customer_email'];
        //Verify Credit Account Infm
        $accountInfo   =  $this->_helper->getARCustomerInfoAction($accountNumber);
        
        if($accountInfo){
            $this->_coreSession->setCurAcc($accountNumber);
            $this->_coreSession->setCustEmail($customerEmail);
            $this->_coreSession->setCustomerInfo($accountInfo);
            $this->coreSession->setPass('test@123');
            $this->_coreSession->setPrevpage('checkout');

            if($accountInfo !== false){              
                $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setUrl($defaultUrl);
            } else{
                $this->messageManager->addErrorMessage(
                    'Curacao Id is wrong...'
                );
                $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setUrl($defaultUrl);
            }

        } else{
            $this->messageManager->addErrorMessage(
                'Curacao Id is wrong...'
            );
            $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            return $resultRedirect->setUrl($defaultUrl);
        }
    }
    $this->messageManager->addErrorMessage(
        'Curacao Id is wrong...'
    );
    $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);
    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
    return $resultRedirect->setUrl($defaultUrl);
  }
}
