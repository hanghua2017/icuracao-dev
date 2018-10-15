<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\CancelOrder\Controller\Test;

use Dyode\CancelOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_cancelOrderHelper;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;
    /***
     * Construct
     *
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Dyode\CancelOrder\Helper\Data $cancelOrderHelper,
        \Dyode\ARWebservice\Helper\Data $arWebServiceHelper
    ) {
        $this->_curl = $curl;
        $this->_cancelOrderHelper = $cancelOrderHelper;
        $this->arWebServiceHelper = $arWebServiceHelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
//        $invoiceNumber = "ZEP58QX";
        $invoiceNumber = $_GET['invNum'];
        $itemId = $_GET['itemId'];
        //$itemId = "32O-285-42LB5600";
        $qty = $_GET['qty'];

        //$qty = 1;
        $inputArray = array(
            "InvNo" => (!empty($invoiceNumber)) ? $invoiceNumber : null,
            "ItemID" => (!empty($itemId)) ? $itemId : null,
            "qty" => 2
        );
        $baseUrl = $this->arWebServiceHelper->getApiUrl();
        $data = json_encode($inputArray);
        $url = $baseUrl . "AdjustItem";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /*
        * Set Content Header
        */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: ' . $this->arWebServiceHelper->getApiKey(),
            'Content-Type: application/json',
            )
        );
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response);
        print_r($result);
        die();

        // $this->_cancelOrderHelper->adjustItem($invoiceNumber, $itemId, $qty);//, $newSubTotal, $newTotalTax, $newPrice, $newDescription);
        // $this->_cancelOrderHelper->adjustItem($invoiceNumber, $itemId, $qty, 30.0, 2.0, 32.0);
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $logger = $objectManager->get("Psr\Log\LoggerInterface");
        // $response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);
        // print_r($response->INFO);
        // $logger->info('Response: ' . $response->INFO);
    }
}