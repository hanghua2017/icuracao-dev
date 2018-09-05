<?php
namespace Category\Widget\Block\Widget;

class Productcollection extends \Magento\Backend\Block\Template
{
  protected $categoryFactory;

  public function __construct(
          \Magento\Backend\Block\Template\Context $context,
          \Magento\Catalog\Model\CategoryFactory $categoryFactory,
           array $data = []
      ){
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context,$data);
    }

    public function getCategoryProduct($categoryId)
    {
        $category = $this->categoryFactory->create()->load($categoryId)->getProductCollection()->addAttributeToSelect('*');
        return $category;
    }
}
