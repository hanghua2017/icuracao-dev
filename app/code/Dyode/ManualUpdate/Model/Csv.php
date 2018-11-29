<?php
/**
 * Copyright © 2017 Terrificminds. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Dyode\ManualUpdate\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\Uploader;

class Csv extends \Magento\Config\Model\Config\Backend\File
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		\Magento\Staging\Api\UpdateRepositoryInterface $updateRepositoryInterface,
        \Magento\Staging\Api\Data\UpdateInterfaceFactory $updateFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, 
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
		\Magento\CatalogStaging\Api\ProductStagingInterface $productStagingInterface,
		\Magento\Staging\Model\VersionManagerFactory $versionManagerFactory,
        array $data = []        
    ) {
        parent::__construct(
        	$context,
            $registry,
            $scopeConfig,
        	$cacheTypeList,
        	$uploaderFactory,
        	$requestData,
        	$filesystem,
        	$resource,
        	$resourceCollection,
        	$data
        ); 
		$this->csv = $csv;
		$this->scopeConfig = $scopeConfig;
    	$this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->updateRepository = $updateRepositoryInterface;
		$this->updateFactory = $updateFactory;
        $this->localeDate = $localeDate;
        $this->_stockRegistry = $stockRegistry;  
		$this->productRepository = $productRepositoryInterface;
		$this->productStaging = $productStagingInterface;
		$this->versionManager = $versionManagerFactory->create();
        $this->_request = $request;
    }

    
    public function savetodb($fileName)
    {
       $fileName = $this->_getUploadDir()."/productdetails.csv";
       $data = $this->csv->getData($fileName);

       if($data)
        {	
	        $headers = $data[0];

	        $productDetails = array();
	        for($i=1; $i<count($data); $i++){
	           $details = array();
	        foreach ($headers as $index=>$attributeCode) {
	           $details[$attributeCode] = $data[$i][$index];
	        }
	           $productDetails[] = $details;     
			}
		    $this->schedule($productDetails);
        }
        return true;
    }

    public function schedule($productData)
	{ 
        foreach ($productData as $data) {
            $sku = $data["Product SKU ID"];
            $campaignName = $data["Campaign Name"];
            $startDate = $data["Campaign Start Date"];
            $endDate = $data["Campaign End Date"];
            $price = $data["Price"];
            $special_price = $data["Special Price"];
            $vendorRebate = $data["Vendor Rebate"];
            $cost = $data["Cost"];
            $customerRebate = $data["Customer Rebate"];
            $inventory = $data["Inventory"];

            $schedule = $this->updateFactory->create();
            $schedule->setName($campaignName);
            $timestampStart = $this->localeDate->scopeTimeStamp() + 3600; 
            $date = new \DateTime(); 
            $schedule->setStartTime($date->format($startDate));
            $schedule->setEndTime($date->format($endDate));
            $stagingRepo = $this->updateRepository->save($schedule);
            $this->versionManager->setCurrentVersionId($stagingRepo->getId());
            $repository = $this->productRepository;
            $product = $repository->get($sku);
            $cost = $cost - $vendorRebate;
            if(!isset($customerRebate) || $customerRebate == 0){
                $customerRebate = 0;
                if($special_price == 0)
                $special_price = '';
            }
            $rebated_price = $price - $customerRebate;
            if (($special_price == 0) || ($rebated_price < $special_price)){
                $special_price = $rebated_price;
            }
            if (0 < $special_price && $special_price < $price) {
                $specialprice = $special_price;
            } else {
                $specialprice = '';
            }
            $product->setStoreId(0);
            $product->setPrice($price);
            $product->setSpecialPrice($specialprice);
            $product->setCost($cost);
            $product->setVendorRebate($vendorRebate);
            $product->setCustomerRebate($customerRebate);
            $product->setInventorylookup('499');
            $product->setCron('492');
            $stockItem = $this->_stockRegistry->getStockItem($product->getID());
            $stockItem->setQty($inventory);
            $stockItem->setIsInStock((bool)$inventory); 
            $this->productStaging->schedule($product, $stagingRepo->getId());   
        }
	}
        /**
    * Save uploaded file before saving config value
    *
    * @return $this
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function beforeSave()
   {
       $this->_getAllowedExtensions();
       $value = $this->getValue();
       $file = $this->getFileData();
       if (!empty($file)) {
           $uploadDir = $this->_getUploadDir();
           try {
               /** @var Uploader $uploader */
               $uploader = $this->_uploaderFactory->create(['fileId' => $file]);
               $uploader->setAllowedExtensions($this->_getAllowedExtensions());
               $result = $uploader->save($uploadDir,"productdetails.csv");
           } catch (\Exception $e) {
               throw new \Magento\Framework\Exception\LocalizedException(__('%1', $e->getMessage()));
           }
           $filename = $result['file'];
           if ($filename) {
               if ($this->_addWhetherScopeInfo()) {
                   $filename = $this->_prependScopeInfo($filename);
               }
               $this->setValue($filename);
               $this->savetodb($filename);
           }
       } else {
           if (is_array($value) && !empty($value['delete'])) {
               $this->setValue('');
           } else {
               $this->unsValue();
           }
       }
       return $this;
   }

   protected function _getUploadDir()
   {
       $fieldConfig = $this->getFieldConfig();
       if (!array_key_exists('upload_dir', $fieldConfig)) {
           throw new \Magento\Framework\Exception\LocalizedException(
               __('The base directory to upload file is not specified.')
           );
       }

       if (is_array($fieldConfig['upload_dir'])) {
           $uploadDir = $fieldConfig['upload_dir']['value'];
           if (
               array_key_exists('scope_info', $fieldConfig['upload_dir'])
               && $fieldConfig['upload_dir']['scope_info']
           ) {
               $uploadDir = $this->_appendScopeInfo($uploadDir);
           }

           if (array_key_exists('config', $fieldConfig['upload_dir'])) {
               $uploadDir = $this->getUploadDirPath($uploadDir);
           }
       } else {
           $uploadDir = (string)$fieldConfig['upload_dir'];
       }
       return $uploadDir;
   }

   protected function _getAllowedExtensions()
   {
       return ['csv'];
   }      
}
