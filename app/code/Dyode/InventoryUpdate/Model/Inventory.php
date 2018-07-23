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
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
	    $this->_orderCollectionFactory = $orderCollectionFactory;
	    $this->_productRepository = $productRepository;
	    $this->helper = $helper;
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

		$staticitems = array('21B-H32-65102401800','21A-J12-F006284809','12S-F94-13003A/AS','20A-R68-MSD32U1HYU','21A-994-A0111603');
		$items = array_chunk($staticitems,1000);

		foreach ($items as $item){
			$skuList = implode(';', $item);	
			foreach ($this->locations as $location) {
				//get inventory details from each location
				$getInventory = $this->helper->batchGetInventory($skuList, $location);
				array_push($this->batchInventory,$getInventory);
			}
		}

		foreach ($this->batchInventory as $inventoryResult) {
			$this->processBatchInventory($inventoryResult);
		}
		print_r($this->skus);exit;
		$this->helper->batchGetInventory($item,'01');
		var_dump($this->productIDs);exit;
	}	

	//get product collection
	public function getProducts() {
	    $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addFieldToFilter('status',['in' => array('01')])->addFieldToFilter('visibility',['in' => array('4')]);
        return $productCollection;
	}

	//processing the API responses
	public function processBatchInventory($inventoryResult){
		$getInventoryResult = json_decode($inventoryResult->BatchGetInventoryResult);
		$items = $getInventoryResult->SKULIST;
		foreach ($items as $item){
			$sku = trim( $item[0] ); //product sku
			$store = $item[1]; //store location
			$stock = $item[2]; //current stock from AR
			$this->productSKUs[$sku][$store] = $stock;
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
		//get threshold values from the corresponding tables
		//$data = SELECT department, sku, threshold FROM curacao_admin.mch_threshold");
		foreach ($data as $item) {	
			$department = $item['department'];
			$sku = trim( $item['sku'] );
			
			if(!isset($sku) || empty($sku)){				
				// Department
				$this->thresh[$department] = $item['threshold'];
			}else{				
				// Sku
				$this->thresh[$sku] = $item['threshold'];
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
					$this->pendingthreshold[$sku][$location] -= $threshold;
					if ($this->pendingthreshold[$sku][$location] < 0){	
						$this->pendingthreshold[$sku][$location] = 0; 
					}
				}
			}
		}
	}

	//execute all available products in the inventory
	public function executeProductSkus(){
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
