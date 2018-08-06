<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\CancelOrder\Helper;

use Dyode\ArInvoice\Helper\Data as ArInvoiceHelperData;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ArInvoiceHelperData
     */
    protected $_arInvoiceHelper;

    /**
     * Construct
     *
     * @param ArInvoiceHelperData $arInvoiceHelper
     */
    public function __construct(
        ArInvoiceHelperData $arInvoiceHelper
    ) {
        $this->_arInvoiceHelper = $arInvoiceHelper;
    }

    public function adjustItem($invNo, $itemId, $qty, $newSubTotal = '', $newTotalTax = '', $newPrice = '', $newDescription = '')
    {
        /**
         * Input Array
         */
        $inputArray = array(
            "InvNo" => $invNo,
            "ItemID" => $itemId,
            "Qty" => $qty,
            "NewSubTotal" => $newSubTotal,
            "NewTotalTax" => $newTotalTax,
            "NewPrice" => $newPrice,
            "NewDescription" => $newDescription
        );
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/AdjustItem";
        $ch = $this->_arInvoiceHelper->initRestApiConnect($url);
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        print_r($response);
        curl_close($ch);
        return json_decode($response);
    }

    public function cancelEstimate($invNo)
    {
        // echo "Hello World123";
        // die();
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/CancelEstimate?Inv_No=$invNo";
        $ch = $this->_arInvoiceHelper->initRestApiConnect($url);
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        print_r($response);
        die();
        curl_close($ch);
        return json_decode($response);
    }
}