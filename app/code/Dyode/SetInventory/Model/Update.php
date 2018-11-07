<?php
namespace Dyode\SetInventory\Model;

use \Magento\Framework\Model\AbstractModel;

class Update extends \Magento\Framework\Model\AbstractModel {
    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
   protected $_productCollectionFactory;
   /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
   protected $products;

   protected $_stockRegistry;
   
   public function __construct( 
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,  
	\Dyode\SetInventory\Helper\Data $helper,
	\Dyode\InventoryLocation\Model\LocationFactory  $inventoryLocation,
	\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
	\Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog,
    \Dyode\ARWebservice\Helper\Data $apiHelper
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
	    $this->helper = $helper;
	    $this->inventorylocation = $inventoryLocation;
	    $this->apiHelper = $apiHelper;
        $this->auditLog = $auditLog;
	    $this->_stockRegistry = $stockRegistry;
	}

	public function setInventoryUpdate(){
		try {
			$clientIP = "";
			$domesticLocation = '06';
			$nonDomesticLocations ='16,33,22,01';
			$this->updateInventory($domesticLocation, 'domestic');
			$this->updateInventory($nonDomesticLocations, 'nondomestic');
	        $this->auditLog->saveAuditLog([
	            'user_id' => 'admin',
	            'action' => 'set inventory update',
	            'description' => 'inventory updated successfully',
	            'client_ip' => $clientIP,
	            'module_name' => 'dyode_setinventory'
	        ]);
	    } catch (\Exception $exception) {
	        $this->auditLog->saveAuditLog([
	            'user_id' => 'admin',
	            'action' => 'set inventory update',
	            'description' => $exception->getMessage(),
	            'client_ip' => $clientIP,
	            'module_name' => 'dyode_setinventory'
	        ]);
	    }
		
	}

	public function updateInventory($location, $type) {
		$products = $this->getProducts($type);
		foreach ($products as $product) {
			$ARsetDescription = $this->helper->getSetItems(utf8_encode($product->getSku()));
			if ($ARsetDescription != 'NULL')
            {
				if ($ARsetDescription->OK){
					$previous = array();
		        $j = 0;
		        foreach ($ARsetDescription->LIST as $component) {
		            $itemId = utf8_encode($component->ITEM_ID);
		            $quantityNeeded = $component->QTY;	            

		            try {
		            	$inventorylevel = $this->helper->inventoryLevel(utf8_encode($itemId),$location);	

		            	if($inventorylevel->OK){

				            $inventoryLevel = array();			
				            $setQuantity = array();
				            $setQuantityTemp = array();

				            foreach ($inventorylevel->LIST as $inventory) {

				                if ($inventory->location != 'TOTAL' && $inventory->quantity != 0) {
				                    $numberOfItemsSets = floor($inventory->quantity / $quantityNeeded);
				                    if ($numberOfItemsSets > 0) {
				                        $setQuantityTemp[$inventory->location] = $numberOfItemsSets;
				                    }
				                    $inventoryLevel[$inventory->location] = $inventory->quantity;
				                }
				            }		
				            if ($j == 0)
				                $previous = $setQuantityTemp;
				            else {
				                $previous = array_intersect_key($previous, $setQuantityTemp);
				                foreach ($previous as $keyPrevious => $valuePrevious) {
				                    if ($valuePrevious > $setQuantityTemp[$keyPrevious])
				                        $previous[$keyPrevious] = $setQuantityTemp[$keyPrevious];
				                }
				            }
				            $j++;
				            $AR_items_inventory_array[$itemId] = $inventoryLevel;
				            $setQuantity[$itemId] = $setQuantityTemp;
			        	}
		            } catch(Exception $ex) {
		            	echo "ERROR: InventoryLevel | $itemId  of  $sku  | " . $ex->getMessage() . " \n";
		            }
		        }
				if (count($previous) > 0)
			            $setQuantity = min($previous); 
			        else
			            $setQuantity = 0;
			        $AR_items_inventory = json_encode($AR_items_inventory_array, true);
			        $locationInventory = $this->inventorylocation->create();
			        $categoryModel = $locationInventory->load($product->getID(), 'productid');
					$data = $categoryModel->getData();
					if($data){
						$model = $locationInventory->load($data['id']);
        				$model->setArinventory($AR_items_inventory);
        				$model->setProductid($product->getID());
        				$model->setProductsku($product->getSku());
        				$saveData = $model->save();	
					} else {
						$locationInventory->addData([
						"productid" => $product->getID(),
						"productsku" => $product->getSku(),
						"isset" => 1,
						"arinventory" => $AR_items_inventory
						]);
						$saveData = $locationInventory->save();
					}
			        $stockItem=$this->_stockRegistry->getStockItem($product->getID());
					$stockItem->setQty($setQuantity);
					$stockItem->setIsInStock((bool)$setQuantity); 
					$stockItem->save();	
					unset($inventoryLevel);
					unset($AR_items_inventory_array);
					unset($AR_items_inventory);
			    }
			}
	    } 
	}	

	public function getProducts($type) {
	    $productCollection = $this->_productCollectionFactory->create();
	    if($type == 'domestic'){
        	$productCollection->addAttributeToSelect('*')
                          ->addAttributeToFilter('inventorylookup', 500)
					      ->addAttributeToFilter('set', 1)
					      ->addAttributeToFilter('shprate','Domestic')
					      ->addAttributeToFilter('vendorId', 2139);    
		} else {
			$productCollection->addAttributeToSelect('*')
                          ->addAttributeToFilter('inventorylookup', 500)
					      ->addAttributeToFilter('set', 1)
					      ->addAttributeToFilter('shprate',['nin' => array('Domestic')])
					      ->addAttributeToFilter('vendorId', 2139); 
		}		                  
        return $productCollection;
	}
}
