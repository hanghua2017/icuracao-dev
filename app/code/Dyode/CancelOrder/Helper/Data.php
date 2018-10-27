<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\CancelOrder\Helper;

use Dyode\ArInvoice\Helper\Data as ArInvoiceHelperData;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ArInvoiceHelperData
     */
    protected $_arInvoiceHelper;

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $arWebServiceHelper;

    /**
     * @var \Dyode\AuditLog\Model\ResourceModel\AuditLog
     */
    protected $auditLog;

    /**
     * Data constructor.
     * @param Context $context
     * @param ArInvoiceHelperData $arInvoiceHelper
     * @param \Dyode\ARWebservice\Helper\Data $arWebServiceHelper
     * @param \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
     */
    public function __construct(
        Context $context,
        ArInvoiceHelperData $arInvoiceHelper,
        \Dyode\ARWebservice\Helper\Data $arWebServiceHelper,
        \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
    ) {
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->arWebServiceHelper = $arWebServiceHelper;
        $this->auditLog = $auditLog;
        parent::__construct($context);
    }

    /**
     * Cancel Order Item using API -> AdjustItem
     *
     * @return Json
     */
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
        $baseUrl = $this->arWebServiceHelper->getApiUrl();
        $url = $baseUrl . "AdjustItem";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $this->arWebServiceHelper->getApiKey(),
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'ArInvoice Adjust Item',
            'description' => "input :" . json_encode($inputArray) . "  response : " . $response,
            'client_ip' => "",
            'module_name' => "Dyode_CancelOrder"
        ]);

        return json_decode($response);
    }

    /**
     * Cancel Order using API -> CancelEstimate
     *
     * @return Json
     */
    public function cancelEstimate($invNo)
    {
        /**
         * Initialize Rest Api Connection
         */
        $baseUrl = $this->arWebServiceHelper->getApiUrl();
        $url = $baseUrl . "CancelEstimate?Inv_No=$invNo";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Api-Key: ' . $this->arWebServiceHelper->getApiKey(),
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $response = curl_exec($ch);
        curl_close($ch);

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'ArInvoice Cancel Estimate',
            'description' => "input : invoice number : " . $invNo . "  response : " . $response,
            'client_ip' => "",
            'module_name' => "Dyode_CancelOrder"
        ]);

        return json_decode($response);
    }
}