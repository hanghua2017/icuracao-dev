<?php
namespace Dyode\InventoryBundle\Model;

use \Magento\Framework\Model\AbstractModel;

class Update extends \Magento\Framework\View\Element\Template {
    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
   protected $_productCollectionFactory;
   /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
   protected $products;
   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,  
	\Dyode\InventoryBundle\Helper\Data $helper,
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
	    $this->helper = $helper;
	    parent::__construct($context, $data);
	}

	public function updateInventory() {
		$products = $this->getProducts();
		foreach ($products as $product) {
			//$this->helper->getSetItems($product->getID());
			$ARsetDescription = $this->helper->getSetItems(utf8_encode('47C-R09-RAZORGR/2PC'));
			if($ARsetDescription->OK){
				$previous = array();
	        $j = 0;
	        foreach ($ARsetDescription->LIST as $component) {
	            $itemId = utf8_encode($component->ITEM_ID);
	            $quantityNeeded = $component->QTY;	            

	            try {
	            	$inventorylevel = $this->helper->inventoryLevel(utf8_encode($itemId),'06');	
	            	$resultInventoryLevel = $inventorylevel ->InventoryLevelResult;	            	
		            
		            $inventoryLevelTemp = explode('\\', $resultInventoryLevel);
		            $inventoryLevel = array();
		
		            $setQuantity = array();
		            $setQuantityTemp = array();
		            foreach ($inventoryLevelTemp as $inventoryLevetLocationTemp) {
		                $explodedInventoryLevetLocationTemp = explode('|', $inventoryLevetLocationTemp);
		                if ($explodedInventoryLevetLocationTemp[0] != 'TOTAL' && $explodedInventoryLevetLocationTemp[1] != 0) {
		                    $numberOfItemsSets = floor($explodedInventoryLevetLocationTemp[1] / $qtyNeeded);
		                    if ($numberOfItemsSets > 0) {
		                        $setQuantityTemp[$explodedInventoryLevetLocationTemp[0]] = $numberOfItemsSets;
		                    }
		                    $inventoryLevel[$explodedInventoryLevetLocationTemp[0]] = $explodedInventoryLevetLocationTemp[1];
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
		            
	            } catch(Exception $ex) {
	            	echo "ERROR: InventoryLevel | $itemId  of  $sku  | " . $ex->getMessage() . " \n";
	            }
	        }
			if (count($previous) > 0)
		            $setQuantity = min($previous); 
		        else
		            $setQuantity = 0;
		        	$AR_items_inventory = json_encode($AR_items_inventory_array, true);
		    }else {
	        $ARsetDescription = json_encode($ARsetDescription, true);
	        // $callNotFound = "CALL icuracaoproduct.setsNotFound('$sku', '$entity_id', '$ARsetDescription')";
	        // $mysqli->query($callNotFound);
	    	}
	    $i++;
	    $ARsetDescription = json_encode($ARsetDescription, true);
	    // $call = "CALL icuracaoproduct.updateSetsInventoryDomestic('$sku', '$entity_id', '$ARsetDescription', '$AR_items_inventory', $set_qty)";
	    // $mysqli->query($call);
	    } 
	}	

	public function getProducts() {
	    $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addFieldToFilter('status',['in' => array('01')])->addFieldToFilter('visibility',['in' => array('4')]);
        return $productCollection;
	}

}
