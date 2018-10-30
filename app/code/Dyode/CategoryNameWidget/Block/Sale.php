<?php

namespace Dyode\CategoryNameWidget\Block;

use \Magento\Framework\View\Element\Template\Context;

class Sale extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var $categoryId
     */
    protected $categoryId;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var $filterCats
     */
    protected $filterCats;

    /**
     * Sale constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
    ) {
        $this->_registry = $registry;
        $this->_categoryFactory = $categoryFactory;
        $this->outputHelper = $outputHelper;
        $this->imageHelper = $imageHelper;
        $this->imageBuilder = $imageBuilder;
        parent::__construct($context);
    }

    /**
     * Retrieving custom variable from registry
     * @return string
     */
    public function getCustomVariable()
    {
        return $this->registry->registry('category');
    }

    /**
     * @return \Magento\Catalog\Helper\Output
     */
    public function helper()
    {
        return $this->outputHelper;
    }

    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        if (!$this->categoryId) {
            $this->fetchCategoryIdFromRequest();
        }

        return $this->categoryInstance()->load($this->categoryId);
    }

    /**
     * @return $this
     */
    protected function fetchCategoryIdFromRequest()
    {
        $category = (int)$this->getRequest()->getParam('category');
        if ($category) {
            $this->categoryId = $category;
        }
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function categoryInstance()
    {
        return $this->_categoryFactory->create();
    }

    /**
     * @return $this
     */
    protected function fetchSaleCategoryIdFromRequest()
    {
        $salecategory = (int)$this->getRequest()->getParam('sale');
        if ($salecategory) {
            $this->salecategoryId = $salecategory;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductCollection()
    {
        $filterCats = (int)$this->getRequest()->getParam('sale');
        return $this->getCategory()
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addCategoriesFilter(array('in' => $filterCats));
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}
