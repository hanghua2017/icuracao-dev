<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/08/2018
 */

namespace Dyode\Checkout\Controller\Curacao;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Math\Random;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlFactory;
use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class Verify extends Action
{

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;

    /** @var \Magento\Framework\UrlFactory */
    protected $urlModel;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        UrlFactory $urlFactory,
        ARWebserviceHelper $helper,
        CheckoutSession $checkoutSession,
        MessageManager $messageManager,
        Random $mathRandom
    ) {
        $this->urlModel = $urlFactory->create();
        $this->_resultFactory = $resultFactory;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
        $this->_checkoutSession = $checkoutSession;
        $this->mathRandom = $mathRandom;

        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $postVariables = (array)$this->getRequest()->getParams();
        if (!empty($postVariables)) {
            //verifying curacao account
            $accountNumber = $postVariables['curacao_account'];
            $accountInfo = $this->_helper->getARCustomerInfoAction($accountNumber);

            if ($accountInfo && $accountInfo !== false) {
                $this->setCuracaoSessionDetails($accountInfo);
                return $this->redirecToScrutinizePage();
            }
        }

        return $this->sendErrorResponse();
    }

    /**
     * Checks whether the request is via ajax or not.
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return (bool)$this->getRequest()->getParam('isAjax', false);
    }

    /**
     * Add curacao information in the core session.
     *
     * * $accountInfo = stdClass(
     *      [CELL] => (323)274-6810
     *      [CITY] => LOS ANGELES
     *      [CUST_ID] => 52398671
     *      [F_NAME] => MARTHA
     *      [L_NAME] => LINARES
     *      [OTHER] => AURELIA
     *      [PHONE] => (323)274-6810
     *      [STATE] => CA
     *      [STREET] => 846 W 42ND PL APT 2
     *      [ZIP] => 90037
     * );
     *
     * @param \stdClass $accountInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setCuracaoSessionDetails($accountInfo)
    {
        $accountNumber = $this->getRequest()->getParam('curacao_account', false);
        $customerEmail = $this->getRequest()->getParam('customer_email', false);
        $curacaoInfo = new DataObject([
            'account_number' => $accountNumber,
            'email_address'  => $customerEmail,
            'first_name'     => $accountInfo->F_NAME,
            'last_name'      => $accountInfo->L_NAME,
            'zip_code'       => $accountInfo->ZIP,
            'previous_page'  => 'checkout',
            'password'       => $this->mathRandom->getRandomString(8),
        ]);

        $this->_checkoutSession->setCuracaoInfo($curacaoInfo);

        return $this;
    }

    /**
     * Redirecting to curacao account verifying page.
     *
     * @return mixed
     */
    protected function redirecToScrutinizePage()
    {
        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $defaultUrl = $this->urlModel->getUrl('linkaccount/verify', ['_secure' => true]);

        return $resultRedirect->setUrl($defaultUrl);
    }

    /**
     * Tells to the world that, curacao information provided is wrong.
     *
     * @return mixed
     */
    protected function sendErrorResponse()
    {
        if ($this->isAjaxRequest()) {
            $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData([
                'message' => 'The given curacao identification number is wrong. Please try again.',
                'type'    => 'error',
            ]);

            return $result;
        }

        $errorMessage = __('The given curacao identification number is wrong. Please try again.');
        $this->messageManager->addErrorMessage($errorMessage);

        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $defaultUrl = $this->urlModel->getUrl('checkout/cart/index', ['_secure' => true]);

        return $resultRedirect->setUrl($defaultUrl);
    }
}
