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
        \Dyode\CancelOrder\Helper\Data $cancelOrderHelper
    ) {
        $this->_curl = $curl;
        $this->_cancelOrderHelper = $cancelOrderHelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $invoiceNumber = "ZEP58QX";
        $itemId = "32O-285-42LB5600";
        $qty = 1;
        $inputArray = array(
            "InvNo" => "ZEP58P6",
            "ItemID" => "09A-RA3-RS16FT5050RB",
            "qty" => 2
        );
        $data = json_encode($inputArray);
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/AdjustItem";
        // $this->_curl->get($url);
        // $this->_curl->addHeader("Content-Type", "application/json");
        // $this->_curl->addHeader("X-Api-Key", "TEST-WNNxLUjBxA78J7s");
        // $this->_curl->curlOption(CURLOPT_SSL_VERIFYHOST, false);
        // $this->_curl->curlOption(CURLOPT_SSL_VERIFYPEER, false);
		// $this->_curl->curlOption(CURLOPT_RETURNTRANSFER, true);
        // $this->_curl->curlOption(CURLOPT_CUSTOMREQUEST, "PUT");
        // $this->_curl->curlOption(CURLOPT_POSTFIELDS, $data);
        // $response = $this->_curl->getBody();
        // print_r($response);
        // die();

        /*
        * Init Curl
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /*
        * Set Content Header
        */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: TEST-WNNxLUjBxA78J7s',
            'Content-Type: application/json',
            )
        );
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
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