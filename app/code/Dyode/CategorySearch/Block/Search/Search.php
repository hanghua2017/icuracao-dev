<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\CategorySearch\Block\Search;

/**
 * Search Block.
 * @category Dyode
 * @package  Dyode_CategorySearch
 * @module   CategorySearch
 * @author   Nithin Mohan A
 */
class Search extends \Magento\Framework\View\Element\Template
{
    /**
     * Search constructor.
     *
     * @param categoryHelper $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryHelper = $categoryHelper;
    }
    /**
     * Retrieve the details of Categories
     *
     * @return array
     */
    public function getStoreCategories()
    {
        return $this->_categoryHelper->getStoreCategories();
    }
}
