<?php

namespace Dyode\PromotionWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CustomWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * construct description
     * @param MagentoFrameworkViewElementTemplateContext $context
     * $data[]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * construct function
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('promotion_block_widget.phtml');
    }

    public function getTitle()
    {
        return $this->getData('blocktitle');

        //it will return your description which is added in your widget
    }

    public function getSelectValue()
    {
        return $this->getData('select_type');
        //will return select option value
    }

    //single image
    public function getClassNameSingle()
    {
      return $this->getData('class_name_single');
    }
    public function getDesktopImageSingle()
    {
      return $this->getData('desktop_image');
    }
    public function getMobileImageSingle()
    {
      return $this->getData('mobile_image');
    }
    public function getReferenceUrlSingle()
    {
      return $this->getData('imageurl');
    }
    //double first image
    public function getClassNameDouble()
    {
      return $this->getData('class_name_double');
    }
    public function getDesktopImageDoubleFirst()
    {
      return $this->getData('desktop_image_double_first');
    }
    public function getMobileImageDoubleFirst()
    {
      return $this->getData('mobile_image_double_first');
    }
    public function getReferenceUrlDoubleFirst()
    {
      return $this->getData('imageurl_double_first');
    }
    //double second image
    public function getDesktopImageDoubleSecond()
    {
      return $this->getData('desktop_image_double_second');
    }
    public function getMobileImageDoubleSecond()
    {
      return $this->getData('mobile_image_double_second');
    }
    public function getReferenceUrlDoubleSecond()
    {
      return $this->getData('imageurl_double_second');
    }
    // triple first image
    public function getClassNameTriple()
    {
      return $this->getData('class_name_triple');
    }
    public function getDesktopImageTripleFirst()
    {
      return $this->getData('desktop_image_triple_first');
    }
    public function getMobileImageTripleFirst()
    {
      return $this->getData('mobile_image_triple_first');
    }
    public function getReferenceUrlTripleFirst()
    {
      return $this->getData('imageurl_trile_first');
    }
    // triple second image
    public function getDesktopImageTripleSecond()
    {
      return $this->getData('desktop_image_triple_second');
    }
    public function getMobileImageTripleSecond()
    {
      return $this->getData('mobile_image_triple_second');
    }
    public function getReferenceUrlTripleSecond()
    {
      return $this->getData('imageurl_triple_second');
    }
    // triple third image
    public function getDesktopImageTripleThird()
    {
      return $this->getData('desktop_image_triple_third');
    }
    public function getMobileImageTripleThird()
    {
      return $this->getData('mobile_image_triple_third');
    }
    public function getReferenceUrlTripleThird()
    {
      return $this->getData('imageurl_triple_third');
    }
    //quadruple first image
    public function getClassNameQuadruple()
    {
      return $this->getData('class_name_quadruple');
    }
    public function getDesktopImagequadrupleFirst()
    {
      return $this->getData('desktop_image_quadruple_first');
    }
    public function getMobileImagequadrupleFirst()
    {
      return $this->getData('mobile_image_quadruple_first');
    }
    public function getReferenceUrlquadrupleFirst()
    {
      return $this->getData('imageurl_quadruple_first');
    }
    //quadruple second image
    public function getDesktopImagequadrupleSecond()
    {
      return $this->getData('desktop_image_quadruple_second');
    }
    public function getMobileImagequadrupleSecond()
    {
      return $this->getData('mobile_image_quadruple_second');
    }
    public function getReferenceUrlquadrupleSecond()
    {
      return $this->getData('imageurl_quadruple_second');
    }
    //quadruple third image
    public function getDesktopImagequadrupleThird()
    {
      return $this->getData('desktop_image_quadruple_third');
    }
    public function getMobileImagequadrupleThird()
    {
      return $this->getData('mobile_image_double_third');
    }
    public function getReferenceUrlquadrupleThird()
    {
      return $this->getData('imageurl_quadruple_third');
    }
    //quadruple fourth image
    public function getDesktopImagequadrupleFourth()
    {
      return $this->getData('desktop_image_quadruple_fourth');
    }
    public function getMobileImagequadrupleFourth()
    {
      return $this->getData('mobile_image_double_fourth');
    }
    public function getReferenceUrlquadrupleFourth()
    {
      return $this->getData('imageurl_quadruple_fourth');
    }
}
