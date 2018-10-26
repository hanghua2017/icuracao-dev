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
                try {
                    $this->setCuracaoSessionDetails($accountInfo);
                    return $this->redirectToScrutinizePage($accountInfo);

                } catch (\Exception $exception) {
                    return $this->sendErrorResponse();
                }
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
     * Add curacao information in the checkout session.
     *
     * @param \stdClass $accountInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setCuracaoSessionDetails($accountInfo)
    {
        $curacaoInfo = $this->prepareOutput($accountInfo);
        $this->_checkoutSession->setCuracaoInfo($curacaoInfo);
        return $this;
    }

    /**
     * Redirecting to curacao account verifying page.
     *
     * @param \stdClass $accountInfo
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function redirectToScrutinizePage($accountInfo)
    {
        $curacaoInfo = $this->prepareOutput($accountInfo);
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'data' => $curacaoInfo->toArray(),
            'type' => 'success',
        ]);

        return $result;
    }

    /**
     * Tells to the world that, curacao information provided is wrong.
     *
     * @return mixed
     */
    protected function sendErrorResponse()
    {
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'message' => __('The given curacao identification number is wrong. Please try again.'),
            'type'    => 'error',
        ]);

        return $result;
    }

    /**
     * Preparing response output.
     *
     * $accountInfo = stdClass(
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
     * @return \Magento\Framework\DataObject $curacaoInfo
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareOutput($accountInfo)
    {
        $accountNumber = $this->getRequest()->getParam('curacao_account', false);
        $customerEmail = $this->getRequest()->getParam('email_address', false);
        $curacaoInfo = new DataObject([
            'account_number' => $accountNumber,
            'email_address'  => $customerEmail,
            'first_name'     => $accountInfo->F_NAME,
            'last_name'      => $accountInfo->L_NAME,
            'zip_code'       => $accountInfo->ZIP,
            'previous_page'  => 'checkout',
            'password'       => $this->mathRandom->getRandomString(8),
        ]);

        return $curacaoInfo;
    }
}
