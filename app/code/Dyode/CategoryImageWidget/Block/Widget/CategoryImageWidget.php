<?php

namespace Dyode\CategoryImageWidget\Block\Widget;

class CategoryImageWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
	protected $_template = 'widget/categorywidget.phtml';

    /**
     * Default value for products count that will be shown
     */
     protected $_categoryHelper;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
     protected $categoryFlatConfig;

    /**
     * @var \Magento\Theme\Block\Html\Topmenu
     */
     protected $topMenu;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
     protected $_categoryFactory;

    /**
     * @var $mainTitle
     */
     protected $mainTitle;

    /**
     * @var $className
     */
     protected $className;

    /**
     * construct
     *
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
     * Get Category model
     *
     * @param $id
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategorymodel($id)
    {
         $_category = $this->_categoryFactory->create();
         $_category->load($id);

         return $_category;
    }

    /**
     * Retrieve collection of selected categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getCategoryCollection()
    {
        $rootCat = $this->getData('parentcat');

        $category = $this->_categoryFactory->create();
        $collection = $category->getCollection()
                      ->addAttributeToSelect('image')
                      ->addIdFilter($rootCat);

        return $collection;
    }

    /**
     * Get Class name
     *
     * @return mixed
     */
    public function getClassName()
    {
        $className = $this->getData('classname');

        return $className;
    }

    /**
     * Get Main Title
     *
     * @return mixed
     */
    public function getMainTitle()
    {
        $mainTitle = $this->getData('blocktitle');

        return $mainTitle;
    }
}
