<?php
namespace Dyode\SetInventory\Model;

use \Magento\Framework\Model\AbstractModel;

class Update extends \Magento\Framework\View\Element\Template {
    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
   protected $_productCollectionFactory;
   /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
   protected $products;

   protected $_stockRegistry;
   
   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,  
	\Dyode\SetInventory\Helper\Data $helper,
	\Dyode\InventoryLocation\Model\LocationFactory  $inventoryLocation,
	\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, 
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
	    $this->helper = $helper;
	    $this->inventorylocation = $inventoryLocation;
	    $this->_stockRegistry = $stockRegistry;
	    parent::__construct($context, $data);
	}

	public function setInventoryUpdate(){
		$domesticLocation = '06';
		$nonDomesticLocations ='16,33,22,01';
		$this->updateInventory($domesticLocation);
		$this->updateInventory($nonDomesticLocations);
	}

	public function updateInventory($location) {
		var_dump("expression");exit;
		$products = $this->getProducts();
		foreach ($products as $product) {
			//var_dump($product->getID());exit;
			//$this->helper->getSetItems($product->getID());
			$ARsetDescription = $this->helper->getSetItems(utf8_encode($product->getSku()));
			if ($ARsetDescription != NULL)
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
				                    $numberOfItemsSets = floor($inventory->quantity / $qtyNeeded);
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
			        $model = $this->inventorylocation->create();
					$model->addData([
						"productid" => $product->getID(),
						"productsku" => $product->getSku(),
						"inventory" => $AR_items_inventory
						]);
			        $saveData = $model->save();
			        $stockItem=$this->_stockRegistry->getStockItem($product->getID());
					$stockItem->setQty($setQuantity);
					$stockItem->setIsInStock((bool)$setQuantity); 
					$stockItem->save();	
			    }
			}
	    } 
	}	

	public function getProducts() {
	    $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
                          ->addFieldToFilter('status',['in' => array('01')])
                          ->addFieldToFilter('visibility',['in' => array('4')]);
                          var_dump("expression");exit;                 
        return $productCollection;
	}
}
