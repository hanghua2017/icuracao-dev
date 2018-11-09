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

class Phonecall extends Action{

    /**
    * @param $customerSession \Magento\Customer\Model\Session
    */
    protected $_customerSession;

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
     * Constructor
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ResultFactory $resultFactory,
        Data $helper,
        CuracaoHelper $curacaoHelper
     ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->curacaoHelper = $curacaoHelper;
     }
 

    public function execute(){
        $customerInfo = $this->curacaoHelper->getCuracaoSessionInformation();
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $output = [];

        if ($customerInfo != null) {
            $phone = $customerInfo->getPhone();
            $resultData = '';
            $resultData = $this->_helper->phoneVerifyCode($phone, 1, 1);
            $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
            $output['curacaoInfo']['phone'] = $phone;

            if($resultData != -1){
                $this->_customerSession->setEncCode($resultData);               
                $output['curacaoInfo']['code']  = $resultData;
                $result->setData([
                    'data' => $output,
                    'type' => 'success',
                ]);
                return $result;
            }
            
            $result->setData([
                'data' => $output,
                'type' => 'error',
            ]);

            return $result;
        }
    }
}
