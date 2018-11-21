<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Helper;

use Magento\Framework\Serialize\Serializer\Json;
use Zend\Http\Request;
use Zend\Http\Headers;
use Zend\Http\Client;
use Dyode\AuditLog\Model\ResourceModel\AuditLog;
use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;

/**
 * Price Helper
 *
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $apiHelper;

    /**
     * @var \Dyode\AuditLog\Model\ResourceModel\AuditLog
     */
    protected $auditLog;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \Zend\Http\Client
     */
    protected $client;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var array
     */
    protected $priceInfo = [];

    /**
     * Data constructor.
     *
     * @param \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
     * @param \Dyode\ARWebservice\Helper\Data $apiHelper
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     */
    public function __construct(AuditLog $auditLog, ARWebserviceHelper $apiHelper, Json $jsonHelper)
    {
        $this->apiHelper = $apiHelper;
        $this->auditLog = $auditLog;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Api call to set stock in AR
     *
     * @param array $productSkuList
     * @param string $module
     * @return bool
     */
    public function sendSetStockARWebserviceRequest($productSkuList, $module)
    {
        $postString = ['list' => $productSkuList];
        $request = $this->prepareRequest('SetEcommerceStock', Request::METHOD_PUT);
        $client = $this->prepareClient();
        $request->setContent(json_encode($postString));

        try {
            $this->addLogs('SetEcommerceStock API request sending', $request, $module);
            $response = $client->send($request);
            $this->addLogs('SetEcommerceStock API response success', $response->getBody(), $module);

            return false;

        } catch (\Exception $e) {
            $this->addLogs('SetEcommerceStock API failed', 'response failed ' . $e, $module);

            return false;
        }
    }

    /**
     * get price details of all products from AR
     *
     * @param string $module
     * @return array|bool
     */
    public function sendUpdatePriceARWebserviceRequest($module)
    {
        $request = $this->prepareRequest('getPrices?top50=false', Request::METHOD_GET);
        $client = $this->prepareClient();
        try {
            $this->addLogs('getPrice API sending', $request, $module);
            $response = $client->send($request);
            $data = $this->jsonHelper->unserialize($response->getBody());

            if (!$data || !isset($data['LIST'])) {
                $this->addLogs('getPrice API failed', 'Repsonse: NULL', 'dyode_priceupdate');

                return false;
            }

            foreach ($data['LIST'] as $listItem) {
                $itemPrice = $listItem['price'];
                $obj = [
                    'storeprice'            => $itemPrice['STOREPRICE'],
                    'special_price'         => $itemPrice['SPECIALPRICE'],
                    'vendorrebate'          => $itemPrice['VENDORREBATE'],
                    'recycling_price'       => $itemPrice['RECYCLEAMOUNT'],
                    'recycling_description' => $itemPrice['RECYCLEINFO'],
                    'cost'                  => $itemPrice['COST'],
                    'msrp'                  => $itemPrice['MSRP'],
                    'sku'                   => utf8_encode(trim($listItem['item_id'])),
                    'iqi'                   => trim($itemPrice['IQI_STATUS']),
                    'ar_status'             => trim($itemPrice['ITEM_TYPE1']),
                ];
                $this->priceInfo[$obj['sku']] = $obj;
            }
            $this->addLogs('getPrice API success', $response->getBody(), 'dyode_priceupdate');
            if ($data['CONTINUE']) {
                $this->sendUpdatePriceARWebserviceRequest();
            }
            $this->addLogs('getPrice API success', $response->getBody(), 'dyode_priceupdate');

            return $this->priceInfo;

        } catch (\Exception $e) {
            $this->addLogs('getPrice API failed', 'connection failed' . $e, 'dyode_priceupdate');

            return false;
        }
    }

    /**
     * Write audit logs
     *
     * @param string $action
     * @param string $description
     * @param string $module
     * @return $this
     */
    public function addLogs($action, $description, $module)
    {
        $clientIP = "";
        $this->auditLog->saveAuditLog([
            'user_id'     => 'admin',
            'action'      => $action,
            'description' => $description,
            'client_ip'   => $clientIP,
            'module_name' => $module,
        ]);

        return $this;
    }

    /**
     * Product price attributes which needs to be updated via webservice.
     *
     * Here array keys represents the reference of attribute in the ARWebservice and
     * array values represent product attributes.
     *
     * @return array
     */
    public function priceUpdateAttributes()
    {
        return [
            'storeprice'        => 'price',
            'special_price'     => 'special_price',
            'cost'              => 'cost',
            'ar_status'         => 'ar_status',
            'special_from_date' => 'special_from_date',
            'special_to_date'   => 'special_to_date',
            'msrp'              => 'msrp',
            'iqi'               => 'iqi',
            'vendorrebate'      => 'vendor_rebate',
            'customerrebate'    => 'customer_rebate',
        ];
    }

    /**
     * Prepare HTTP API request to send
     *
     * @param string $apiMethod
     * @param string $methodType
     * @return \Zend\Http\Request
     */
    protected function prepareRequest($apiMethod, $methodType)
    {
        if (!$this->request) {
            $apiKey = $this->apiHelper->getApiKey();
            $apiUrl = $this->apiHelper->getApiUrl();

            $httpHeaders = new Headers();
            $httpHeaders->addHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'X-Api-Key'    => $apiKey,
            ]);
            $request = new Request();
            $request->setHeaders($httpHeaders);
            $this->request = $request;
        }

        $this->request->setUri($apiUrl . $apiMethod);
        $this->request->setMethod($methodType);

        return $this->request;
    }

    /**
     * Prepare client
     *
     * @return \Zend\Http\Client
     */
    protected function prepareClient()
    {
        if (!$this->client) {
            $client = new Client();
            $options = [
                'adapter'      => 'Zend\Http\Client\Adapter\Curl',
                'curloptions'  => [CURLOPT_FOLLOWLOCATION => true],
                'maxredirects' => 0,
                'timeout'      => 360,
            ];
            $client->setOptions($options);
            $this->client = $client;
        }

        return $this->client;
    }
}  
