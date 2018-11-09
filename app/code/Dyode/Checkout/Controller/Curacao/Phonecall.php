<?php
/**
 * Dyode_Checkout Magento2 Module.
 *
 *
 * @package Dyode
 * @module  Dyode_Checkout
 * @author  Kavitha <kavitha@dyode.com>
 * @date    12/07/2018
 * @copyright Copyright © Dyode
 */

namespace Dyode\Checkout\Controller\Curacao;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Dyode\ARWebservice\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Dyode\Checkout\Helper\CuracaoHelper;

class Phonecall extends Action
{

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var Dyode\ARWebservice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * Phonecall constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        Data $helper,
        CuracaoHelper $curacaoHelper
    ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_helper = $helper;
        $this->curacaoHelper = $curacaoHelper;
    }

    /**
     * Main entry point.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerInfo = $this->curacaoHelper->getCuracaoSessionInformation();
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $output = [];

        if ($customerInfo != null) {
            $phone = $customerInfo->getPhone();
            $resultData = $this->_helper->phoneVerifyCode($phone, 1, 1);
            $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
            $output['curacaoInfo']['phone'] = $phone;

            if ($resultData != -1) {
                $this->curacaoHelper->updateCuracaoSessionDetails(['enc_code' => $resultData]);
                $output['curacaoInfo']['code'] = $resultData;
                $result->setData([
                    'data' => $output,
                    'type' => 'success',
                ]);
                return $result;
            }
        }

        $result->setData([
            'data' => $output,
            'type' => 'error',
        ]);

        return $result;
    }
}
