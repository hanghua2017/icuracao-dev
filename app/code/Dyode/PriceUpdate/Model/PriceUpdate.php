<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Price Model
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class PriceUpdate extends \Magento\Framework\View\Element\Template
{

    public $productCollectionFactory;

    public $skuList = array();

    public $skus = array();

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Dyode\PriceUpdate\Helper\Data $helper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * function name : updatePrice
     * definition : cron model function for updatingprice
     * @return no return
     */
    public function updatePrice()
    {
        $this->getProductSkulist();
        $this->getBatchPrice();
        $this->processBatchprice();
    }

    /**
     * function name : getProductSkulist
     * definition : get sku and product id of all products
     * @param int $storeViewId
     * @return no return
     */
    public function getProductSkulist()
    {
        $productCollection = $this->productCollectionFactory->create();
        /** Apply filters here */
        $productCollection->addAttributeToSelect('*')
        ->addAttributeToFilter('vendorId', 2139)
        ->addAttributeToFilter('cron', 13);
        foreach ($productCollection as $product) {
            $sku = utf8_encode(strtoupper(trim($product->getSku())));
            $this->skuList[$sku]['entity_id'] = $product->getId();
        }
    }

    /**
     * function name : getBatchPrice
     * definition : get price details of all products from AR
     * @return no return
     */
    public function getBatchPrice()
    {

        try {
            $httpHeaders = new \Zend\Http\Headers();
            $httpHeaders->addHeaders([
               'Accept' => 'application/json',
               'Content-Type' => 'application/json',
               'X-Api-Key' => 'TEST-WNNxLUjBxA78J7s'
            ]);

            $request = new \Zend\Http\Request();
            $request->setHeaders($httpHeaders);
            $request->setUri('https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/getPrices?top50=true');
            $request->setMethod(\Zend\Http\Request::METHOD_GET);

            $client = new \Zend\Http\Client();
            $options = [
               'adapter'   => 'Zend\Http\Client\Adapter\Curl',
               'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
               'maxredirects' => 0,
               'timeout' => 30
            ];
            $client->setOptions($options);
            $response = $client->send($request);
            $data = json_decode($response->getBody());
            $list = $data->LIST;
            foreach ($list as $key) {
                $obj = array(
                  'storeprice' => $key->price->STOREPRICE,
                  'special_price' => $key->price->SPECIALPRICE,
                  'vendorrebate' => $key->price->VENDORREBATE,
                  'customerrebate' => $key->price->CUSTOMERREBATE,
                  'recycling_price' => $key->price->RECYCLEAMOUNT,
                  'recycling_description' => $key->price->RECYCLEINFO,
                  'cost' => $key->price->COST,
                  'msrp' => $key->price->MSRP,
                  'sku' => utf8_encode(trim($key->item_id)),
                  'iqi' => trim( $key->price->IQI_STATUS ),
                  'ar_status' => trim( $key->price->ITEM_TYPE1 )
                );
                $this->skus[ $obj['sku'] ]= $obj;
            }
            if ($data->CONTINUE) {
               $this->getBatchPrice();            	
            }            
        }
        catch (\Exception $e) {
            return false;
        }
    } 

    /**
     * function name : processBatchprice
     * definition : update price details of all products
     * @return no return
     */
    public function processBatchprice()
    {
        $special_to_date = date('Y-m-d', strtotime("+2 days"));
        $special_from_date = date('Y-m-d', time());
        foreach ($this->skus as $item) {
            $sku = utf8_encode(strtoupper(trim($item['sku'])));
            if (array_key_exists($sku, $this->skuList)) {
            $entity_id = $this->skuList[$sku]['entity_id'];
                if (!empty($entity_id)) {
                    $price = $item['storeprice'];
                    $special_price = $item['special_price'];
                    $vendor_rebate = $item['vendorrebate'];
                    $customer_rebate = $item['customerrebate'];
                    $recycling_price = $item['recycling_price'];
                    $recycling_description = $item['recycling_description'];
                    $cost = $item['cost'];
                    $msrp = $item['msrp'];
                    $sku = $item['sku'];
                    $iqi = $item['iqi'];
                    $ar_status = $item['ar_status'];

                    $rebated_price = $price - $customer_rebate;
                    $cost = $cost - $vendor_rebate;
                    if (($special_price == 0) || ($rebated_price < $special_price)){
                        $special_price = $rebated_price;
                    }
                    if (0 < $special_price && $special_price < $price) {
                        $specialprice = $special_price;
                        $specialfromdate = $special_from_date;
                        $specialtodate = $special_to_date;          
                    } else {
                        $specialprice = '';
                        $specialfromdate = '';
                        $specialtodate = '';
                    }
                    $product = $this->productRepository->getById($entity_id);
                    $product->setPrice($price);
                    $product->setSpecialPrice($specialprice);
                    $product->setCost($cost);
                    $product->setArStatus($ar_status);
                    $product->setSpecialFromDate($specialfromdate);
                    $product->setSpecialToDate($specialtodate);
                    $product->setMsrp($msrp);
                    $product->setIqi($iqi);
                    $product->setRecyclingprice($recycling_price);
                    $product->setRecyclingdescription($recycling_description);
                    $product->setVendorRebate($vendor_rebate);
                    $product->setCustomerRebate($customer_rebate);
                    $product->save();
                }
            } 
        }
    }
}