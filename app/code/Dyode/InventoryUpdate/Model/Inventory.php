<?php
namespace Dyode\InventoryUpdate\Model;

use \Magento\Framework\Model\AbstractModel;

class Inventory extends \Magento\Framework\View\Element\Template {

   protected $_productCollectionFactory;

   protected $_productRepository;

   protected $products;

   //Not Domestic locations
   public $locations = array('01', '09', '16', '22', '29', '33', '35', '38', '40', '51', '57', '64');  

   public $productSKUs = array();

   public $productIDs = array();

   public $list = array(); 

   public $batchInventory = array();

   public $pending = array();  

   public $thresh = array();

   public $pendingthreshold = array();

   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,  
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory, 
	\Dyode\InventoryUpdate\Helper\Data $helper,
	\Dyode\Threshold\Model\Threshold $thresholdModel,
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
	    $this->_orderCollectionFactory = $orderCollectionFactory;
	    $this->_productRepository = $productRepository;
	    $this->helper = $helper;
	    $this->threshold = $thresholdModel;
	    parent::__construct($context, $data);
	}

	public function updateInventory() {

		$products = $this->getProducts();
		foreach ($products as $product) {
			$productSKU = trim($product->getSku());
			$productId = trim($product->getId());
			$this->list[] = $productSKU;
			$this->productSKUs[$productSKU] = array();
			$this->productIDs[$productSKU] = $productId;
		}

		$this->processBatchInventory();
		$this->getAllPending();
		$this->getAllThreshold();
		$this->processThreshold();
		$this->executeProductSkus();
	}	

	//get product collection
	public function getProducts() {
	    $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addFieldToFilter('status',['in' => array('01')])->addFieldToFilter('visibility',['in' => array('4')]);
        return $productCollection;
	}

	//processing the API responses
	public function processBatchInventory(){
		$arInventory = $this->helper->getStock();
		if($arInventory){
		if($arInventory->OK){
			foreach ($arInventory->LIST as $item) {
				$sku = trim( $item->item_id ); //product sku
				foreach ($item->stock as $location => $quantity) {
					$store = $location; //store location
			        $stock = $quantity; //current stock from AR
			        $this->productSKUs[$sku][$store] = $stock;
				}
			}
		}
		if ($arInventory->CONTINUE) {
			$this->processBatchInventory();
		}
	    }
	}	

	// Get all pending order items
	public function getAllPending(){	
		$to = date("Y-m-d h:i:s"); // current date
    	$from = strtotime('-60 day', strtotime($to));
    	$from = date('Y-m-d h:i:s', $from); //60 days before
    	$orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')
    	->addFieldToFilter('status',['nin' => array('incomplete','canceled','complete','closed')])
	    ->addFieldToFilter('created_at', array('from'=>$from, 'to'=>$to));

	    $this->pending = unserialize(serialize($this->productSKUs));
	   
	    foreach ($orders as $order) {
	    	$orderItems = $order->getAllItems();
		    foreach ($orderItems as $items) {
		    	$pending = $items['qty_ordered'] - $items['qty_invoiced']; 
		    	$sku = trim( $items['sku'] );
				$store = $items['store_id'];
				if(isset($this->pending[$sku][$store])){
					$this->pending[$sku][$store] -= $pending;
					if ($this->pending[$sku][$store] < 0){ $this->pending[$sku][$store] = 0; }
				}
		    }
	    }

	}

	//Get all threshold values for the products
	public function getAllThreshold(){

		$data = $this->threshold->getThreshold();
		foreach ($data as $item) {	
			$department = $item['Sub Departments Name'];
			$sku = trim( $item['Sub Code'] );
			
			if(!isset($sku) || empty($sku)){				
				// Department
				$this->thresh[$department] = $item['Threshold'];
			}else{				
				// Sku
				$this->thresh[$sku] = $item['Threshold'];
			}
		}
	}

	public function processThreshold(){
		$this->pendingthreshold = unserialize(serialize($this->pending));
		foreach ($this->pendingthreshold as $sku => $locations){
			foreach ($locations as $location => $pendingInLoc){
				if ($location !== 33){
					$trimmedSku = substr($sku, 0, 3);
					$trimmedSku = trim($trimmedSku); // To fix threshold 0 issue with 2-character departments
					if (isset($this->thresh[$sku])){
						$threshold = $this->thresh[$sku];
					}
					else if (isset($this->thresh[$trimmedSku])){
						$threshold = $this->thresh[$trimmedSku];
					}else{
						$threshold = 0;
					}

					$this->pendingthreshold[$sku][$location] -= (int)$threshold;
					if ($this->pendingthreshold[$sku][$location] < 0){	
						$this->pendingthreshold[$sku][$location] = 0; 
					}
				}
			}
		}
	}

	//execute all available products in the inventory
	public function executeProductSkus(){
		foreach ($this->productSKUs as $sku => $inv)
		{			
			if (isset($this->productIDs[$sku]))
			{
				$eid = $this->productIDs[$sku];
				if (count($inv) == 0)
				{
										
				}
				else
				{
					$jsonAR_inv = json_encode($inv, true);
					$jsonAR_invAfterPending = json_encode($this->pending[$sku], true);
					$jsonAR_invAfterPendingAndThreshold = json_encode($this->pendingthreshold[$sku], true);
					$finalInv = max($this->pendingthreshold[$sku]);
					$finalLocation = array_search($finalInv, $this->pendingthreshold[$sku]);
					
					// Company-wide Inventory
					$inventory_values = array_values($this->pendingthreshold[$sku]);
					$company_wide_inventory = array_sum($inventory_values);
					var_dump($company_wide_inventory);exit;
				}
			}
		}
		$productCollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
		foreach($productCollection as $product) {
			//$item = $this->_productRepository->getById($product->getId());
			// if($product->getDiscontinued()){
				//$item->setData('status',0);
				//$item->setData('visibility',0);
				//$item->setData('inventory_lookup','499');
				//$item->setData('run_cron','492');
            	//$this->_productRepository->save($item);
				//$product->setDiscontinued('0'); 
				// $product->getResource()->saveAttribute($product,'status');
				// $product->setData('visibility',0);
				// $product->getResource()->saveAttribute($product,'visibility');
				// $product->setData('inventory_lookup',499);
				// $product->getResource()->saveAttribute($product,'inventory_lookup');
				// $product->getResource()->saveAttribute($product,'discontinued');
				// $product->setData('run_cron',492);
    			//$productCollection->save($product);
    			var_dump("expression");exit;
			//}
		}
	}
	
}
