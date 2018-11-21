<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Dyode\PriceUpdate\Helper\Data as PriceHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

/**
 * Price Model
 *
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class PriceUpdate
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $priceProductCollection;

    /**
     * @var \Dyode\PriceUpdate\Helper\Data
     */
    protected $helper;

    /**
     * @var []
     */
    protected $skuList;

    /**
     * @var []
     */
    protected $skuApiList;

    /**
     * PriceUpdate constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Dyode\PriceUpdate\Helper\Data $priceHelper
     */
    public function __construct(ProductCollectionFactory $productCollectionFactory, PriceHelper $priceHelper)
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helper = $priceHelper;
    }

    /**
     * Updating products price via cron
     *
     * @return bool
     */
    public function updatePrice()
    {
        try {
            $skuList = $this->getProductSkuList();
            $setStockResponse = $this->helper->sendSetStockARWebserviceRequest($skuList, 'dyode_priceupdate');
            if (!$setStockResponse) {
                return false;
            }

            $updatePriceResponse = $this->helper->sendUpdatePriceARWebserviceRequest('dyode_priceupdate');
            if (!$updatePriceResponse) {
                return false;
            }

            $this->updateProductPriceInfo($updatePriceResponse);
            $this->helper->addLogs('price update', 'price updated successfully', 'dyode_priceupdate');

            return true;
        } catch (\Exception $exception) {
            $this->helper->addLogs('price update', 'price update failed ' . $exception, 'dyode_priceupdate');

            return false;
        }
    }

    /**
     * get sku and product id of all products
     *
     * @param boolean $api
     * @return array $skuList
     */
    public function getProductSkuList($api = true)
    {
        if (!$this->skuList || !$this->skuApiList) {
            $count = 0;
            $skuList = [];
            $skuList4Api = [];

            foreach ($this->productCollectionForPriceUpdate() as $product) {
                $sku = utf8_encode(strtoupper(trim($product->getSku())));
                $status = $product->getStatus();
                $skuList[$sku]['entity_id'] = $product->getId();
                $skuList4Api[$count]['sku'] = $product->getSku();
                if ($status) {
                    $skuList4Api[$count]['active'] = true;
                } else {
                    $skuList4Api[$count]['active'] = false;
                }
                $count++;
            }

            $this->skuList = $skuList;
            $this->skuApiList = $skuList4Api;
        }

        if ($api) {
            return $this->skuApiList;
        }

        return $this->skuList;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function productCollectionForPriceUpdate()
    {
        if (!$this->priceProductCollection) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect(['sku', 'status', 'vendorid', 'cron'])
                ->addAttributeToSelect(array_values($this->helper->priceUpdateAttributes()))
                ->addAttributeToFilter('vendorid', 2139)
                ->addAttributeToFilter('cron', 493)
                ->setPageSize(3)
                ->getCurPage(1);

            $this->priceProductCollection = $productCollection;
        }

        return $this->priceProductCollection;
    }

    /**
     * Update price details of all products
     *
     * @param array $priceInfo
     */
    public function updateProductPriceInfo(array $priceInfo)
    {
        $special_to_date = date('Y-m-d', strtotime("+2 days"));
        $special_from_date = date('Y-m-d', time());
        $store = 0;

        foreach ($priceInfo as $item) {
            $sku = utf8_encode(strtoupper(trim($item['sku'])));

            if (!array_key_exists($sku, $this->getProductSkuList(false))) {
                continue;
            }

            $productId = $this->skuList[$sku]['entity_id'];
            if (!empty($productId)) {
                /** @var \Magento\Catalog\Model\Product $product */
                $itemPriceInfo = new DataObject($item);
                $product = $this->productCollectionForPriceUpdate()->getItemById($productId);
                $updateAttributes = [];
                $exclude = ['cost', 'special_price', 'special_from_date', 'special_to_date'];

                //collect attributes whose values really changed from the current product attribute value.
                foreach ($this->helper->priceUpdateAttributes() as $apiKey => $productKey) {
                    if ($itemPriceInfo->getData($apiKey)
                        && !in_array($productKey, $exclude)
                        && $itemPriceInfo->getData($apiKey) != $product->getData($productKey)
                    ) {
                        $updateAttributes[] = $apiKey;
                    }
                }

                //checks the 'cost' attribute value is changed.
                if (!$this->isCostSame($itemPriceInfo, $product)) {
                    $updateAttributes[] = 'cost';
                }

                //checks the 'special_price' attribute value is changed.
                if (!$this->isSpecialPriceSame($itemPriceInfo, $product)) {
                    $updateAttributes[] = 'special_price';
                    $updateAttributes[] = 'special_from_date';
                    $updateAttributes[] = 'special_to_date';
                }

                //Update only changed attribute values.
                foreach ($updateAttributes as $attribute) {
                    if ($attribute === 'cost') {
                        $product->addAttributeUpdate('cost', $this->calculateProductCost($itemPriceInfo), $store);
                    } elseif ($attribute === 'special_price') {
                        $product->addAttributeUpdate('special_price', $this->calculateSpecialPrice($itemPriceInfo),
                            0);
                    } elseif ($attribute === 'special_from_date') {
                        $product->addAttributeUpdate('special_from_date', $special_from_date, $store);
                    } elseif ($attribute === 'special_to_date') {
                        $product->addAttributeUpdate('special_to_date', $special_to_date, $store);
                    } else {
                        $product->addAttributeUpdate($attribute, $itemPriceInfo->getData($attribute), $store);
                    }
                }
            }
        }
    }

    /**
     * Calculating cost Price of the product from the api response.
     *
     * @param \Magento\Framework\DataObject $priceInfo
     * @return float
     */
    protected function calculateProductCost(DataObject $priceInfo)
    {
        $cost = (float)$priceInfo->getCost();
        if ($priceInfo->getCost() && $priceInfo->getVendorrebate()) {
            $cost = (float)($priceInfo->getCost() - $priceInfo->getVendorrebate());
        }

        return $cost;
    }

    /**
     * Calculate Special price from the api response
     *
     * @param \Magento\Framework\DataObject $priceInfo
     * @return float
     */
    protected function calculateSpecialPrice(DataObject $priceInfo)
    {
        $price = (float)$priceInfo->getStoreprice();
        $special_price = (float)$priceInfo->getSpecialPrice();
        $customer_rebate = (float)$priceInfo->getCustomerrebate();
        $rebated_price = $price - $customer_rebate;
        if (($special_price == 0) || ($rebated_price < $special_price)) {
            $special_price = $rebated_price;
        }

        return $special_price;
    }

    /**
     * Checks whether the product 'cost' and priceInfo 'cost' are same.
     *
     * @param \Magento\Framework\DataObject $priceInfo
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function isCostSame(DataObject $priceInfo, Product $product)
    {
        $cost = (float)$priceInfo->getCost();
        if ($priceInfo->getCost() && $priceInfo->getVendorrebate()) {
            $cost = (float)($priceInfo->getCost() - $priceInfo->getVendorrebate());
        }

        return $cost == (float)$product->getPrice();
    }

    /**
     * Checks whether the product 'special_price' and PriceInfo 'special_price' are same.
     *
     * @param \Magento\Framework\DataObject $priceInfo
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function isSpecialPriceSame(DataObject $priceInfo, Product $product)
    {
        $price = (float)$priceInfo->getStoreprice();
        $special_price = (float)$priceInfo->getSpecialPrice();
        $customer_rebate = (float)$priceInfo->getCustomerrebate();
        $rebated_price = $price - $customer_rebate;
        if (($special_price == 0) || ($rebated_price < $special_price)) {
            $special_price = $rebated_price;
        }
        if (0 < $special_price && $special_price < $price) {
            if ($special_price != (float)$product->getSpecialPrice()) {
                return false;
            }
        }

        return true;
    }
}
