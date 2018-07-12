<?php
namespace Dyode\InventoryUpdate\Model;

use \Magento\Framework\Model\AbstractModel;

class Inventory extends \Magento\Framework\View\Element\Template {

   protected $_productCollectionFactory;

   protected $products;

   public $locations = array('01', '09', '16', '22', '29', '33', '35', '38', '40', '51', '57', '64');  

   public $productSKUs = array();

   public $productIDs = array();

   public $list = array();    

   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,  
	\Dyode\InventoryUpdate\Helper\Data $helper,
	array $data = []
	) {
	    $this->_productCollectionFactory = $productCollectionFactory;  
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

		$items = array('21B-H32-65102401800','21A-J12-F006284809','12S-F94-13003A/AS','20A-R68-MSD32U1HYU','21A-994-A0111603');
		$itemsc = array_chunk($items,1000);


		foreach ($itemsc as $item)
		{
			
			
			$s = implode(';', $item);	

			var_dump($s);
	
				$this->helper->batchGetInventory($s,'09');				
				

		}


		$this->helper->batchGetInventory($item,'01');
		var_dump($this->productIDs);exit;
	}	

	public function getProducts() {
	    $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addFieldToFilter('status',['in' => array('01')])->addFieldToFilter('visibility',['in' => array('4')]);
        return $productCollection;
	}

	
}
