<?php
namespace Dyode\CategoryNameWidget\Block\Widget;

class CategoryNameWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_template = 'widget/categorywidget.phtml';

    /**
     * Default value for products count that will be shown
     */
     protected $_categoryHelper;
     protected $categoryFlatConfig;
     protected $topMenu;
     protected $_categoryFactory;
     protected $mainTitle;
     protected $className;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Theme\Block\Html\Topmenu $topMenu
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->topMenu = $topMenu;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }
    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }
		/**
		 * Return category
		 */
    public function getCategorymodel($id)
    {
         $_category = $this->_categoryFactory->create();
            $_category->load($id);
            return $_category;
    }
    /**
		  * Retrieve collection of selected categories
      */
   public function getCategoryCollection()
    {
        $rootCat    = $this->getData('parentcat');
        $category   = $this->_categoryFactory->create();
        $collection = $category
                      ->getCollection()
                      ->addAttributeToSelect('image')
                      ->addIdFilter($rootCat);
        return $collection;
    }
		/**
		  * function name : getSaleCategory
      * Retrieve getSaleCategory
		  *
      */
		public function getSaleCategory()
		{
			$salecategory =$this->getData('salecat');
			return $salecategory;
		}
		/**
		* function name : getMainTitle
     * Retrieve blocktitle
    */
    public function getMainTitle()
    {
        $mainTitle = $this->getData('blocktitle');
        return $mainTitle;
    }
}