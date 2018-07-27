<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ShippingOrder\Helper;

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

    /**
     * Supply Web Item using API -> SupplyWebItem
     */
    public function supplyWebItem($invNo, $itemId, $itemName, $qty, $isSet)
    {
        /**
         * Input Array
         */
        $inputArray = array(
            "InvNo" => $invNo,
            "ItemID" => $itemId,
            "ItemName" => $itemName,
            "Qty" => $qty,
            "IsSet" => $isSet,
        );
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/SupplyWebItem";
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
        curl_close($ch);
        return json_decode($response);
    }

    /**
     * Get Inventory Level using API -> Inventory Level
     */
    public function getInventoryLevel($itemId, $locations)
    {
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/InventoryLevel?item_id=$itemId&locations=$locations";
        $ch = $this->_arInvoiceHelper->initRestApiConnect($url);
        /**
         * Set Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}