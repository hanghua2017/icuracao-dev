<?php
namespace Dyode\CategoryNameWidget\Controller\Sale;
use Magento\Framework\App\Action\Context;

class Saleproducts extends \Magento\Framework\App\Action\Action
  {
    protected $_resultPageFactory;
    protected $_registry;
    protected $_categoryFactory;
     public function __construct(Context $context,
                                 \Magento\Framework\Registry $registry,
                                 \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                 \Magento\Catalog\Model\CategoryFactory $categoryFactory
                                 )
     {
         $this->_resultPageFactory = $resultPageFactory;
         $this->_registry          = $registry;
         $this->_categoryFactory   =$categoryFactory;
         parent::__construct($context);
     }

     public function execute()
     {
         $resultPage = $this->_resultPageFactory->create();
         $this->setCategory();
         $this->setsaleCategory();
         return $resultPage;
     }
     public function categoryInstance()
     {
       return $this->_categoryFactory->create();
     }
     /**
       * Setting custom variable in registry to be used
       *
       */
      public function setCategory()
       {
     $category=$this->categoryInstance()->load($this->getRequest()->getParam('category'));
     $this->_registry->register('current_category', $category);
      return $this;
        }
    public function setsaleCategory(){
      // $salecategory=
      $salecategory=$this->categoryInstance()->load($this->getRequest()->getParam('sale'));
      return $salecategory;
    }
 }
