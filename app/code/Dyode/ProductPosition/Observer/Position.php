<?php
namespace Dyode\ProductPosition\Observer;

class Position implements \Magento\Framework\Event\ObserverInterface
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
     //$order= $observer->getData('order');
	 //$order->doSomething();
  	$product = $observer->getProduct();

  	
  	/*
     * Below code used to update category of particular products position 
     */
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //$categoryId = 6; //replace with your categoryId
    $newPosition = $product->getSort(); //replace with your new position
    $categoryIds = $product->getCategoryIds();
    foreach ($categoryIds as $categoryId) {
    	
    	$category = $objectManager->get('\Magento\Catalog\Model\CategoryFactory')->create()->load($categoryId);
    $products = $category->getProductsPosition();

    $pid = [];

  
         $name = $product->getname();
            $pid[] = $product->getId();

    foreach ($products as $id=>$value){  
        if(in_array($id,$pid)){  
            $products[$id] = $newPosition;
        }
    }
    $category->setPostedProducts($products);
    $category->save();

    }

     return $this;
  }
}