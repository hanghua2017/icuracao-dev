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
    /**
    * function name : getTitle of the widget
     * Retrieve tile of the block
     *
    */
    public function getTitle()
    {
        return $this->getData('blocktitle');


    }
    /**
    * function name : getSelectValue
     * Retrieve value of drop down
     *
    */

    public function getSelectValue()
    {
        return $this->getData('select_type');

    }

    /**
    * function name : getClassNameSingle
     * Retrieve classname
     *
    */


    //single image
    public function getClassNameSingle()
    {
      return $this->getData('class_name_single');

    }
    /**
    * function name : getDesktopImageSingle
     * Retrieve desktop image
     *
    */
    public function getDesktopImageSingle()
    {
      return $this->getData('desktop_image');
    }
    /**
    * function name : getMobileImageSingle
     * Retrieve  mobile image
     *
    */
    public function getMobileImageSingle()
    {
      return $this->getData('mobile_image');

    }
    /**
    * function name : getReferenceUrlSingle
     * Retrieve url
     *
    */
    public function getReferenceUrlSingle()
    {
      return $this->getData('imageurl');

    }
    /**
    * function name : getClassNameDouble
     * Retrieve classname
     *
    */
    //double first image
    public function getClassNameDouble()
    {
      return $this->getData('class_name_double');

    }
    /**
    * function name : getDesktopImageDoulbefirst
     * Retrieve desktop image
     *
    */
    public function getDesktopImageDoubleFirst()
    {
      return $this->getData('desktop_image_double_first');
    }

    /**
    * function name : getMobileImageDoubleFirst
    * Retrieve mobile image
    *
    */
    public function getMobileImageDoubleFirst()
    {
      return $this->getData('mobile_image_double_first');

    }
    /**
    * function name : getReferenceUrlDoubleFirst
     * Retrieve image url
     *
    */
    public function getReferenceUrlDoubleFirst()
    {
      return $this->getData('imageurl_double_first');
   }
   /**
   * function name : getDesktopImageDoubleSecond
    * Retrieve desktop image
    *
   */
    public function getDesktopImageDoubleSecond()
    {
      return $this->getData('desktop_image_double_second');

    }
    /**
    * function name : getMobileImageDoubleSecond
     * Retrieve mobile image
     *
    */
    public function getMobileImageDoubleSecond()
    {
      return $this->getData('mobile_image_double_second');

    }
    /**
    * function name : getReferenceUrlDoubleSecond
     * Retrieve reference url
     *
    */
    public function getReferenceUrlDoubleSecond()
    {
      return $this->getData('imageurl_double_second');

    }
    /**
    * function name : getClassNameTriple
     * Retrieve classname
     *
    */
    // triple first image
    public function getClassNameTriple()
    {
      return $this->getData('class_name_triple');

    }
    /**
    * function name : getDesktopImageTripleFirst
     * Retrieve desktop image
     *
    */
    public function getDesktopImageTripleFirst()
    {
      return $this->getData('desktop_image_triple_first');

    }
    /**
    * function name : getMobileImageTripleFirst
     * Retrieve mobile image
     *
    */
    public function getMobileImageTripleFirst()
    {
      return $this->getData('mobile_image_triple_first');

    }
    /**
    * function name : getReferenceUrlTripleSecond
     * Retrieve reference url
     *
    */
    public function getReferenceUrlTripleFirst()
    {
      return $this->getData('imageurl_trile_first');

    }
    /**
    * function name : getDesktopImageTripleSecond
     * Retrieve desktop image
     *
    */
    // triple second image
    public function getDesktopImageTripleSecond()
    {
      return $this->getData('desktop_image_triple_second');

    }
    /**
    * function name : getMobileImageTripleSecond
     * Retrieve mobile image
     *
    */
    public function getMobileImageTripleSecond()
    {
      return $this->getData('mobile_image_triple_second');

    }
    /**
    * function name : getReferenceUrlTripleSecond
     * Retrieve reference url
     *
    */
    public function getReferenceUrlTripleSecond()
    {
      return $this->getData('imageurl_triple_second');

    }
    /**
    * function name : getDesktopImageTripleThird
     * Retrieve Desktop image
     *
    */
    // triple third image
    public function getDesktopImageTripleThird()
    {
      return $this->getData('desktop_image_triple_third');

    }
    /**
    * function name : getMobileImageTripleThird
     * Retrieve mobile image
     *
    */
    public function getMobileImageTripleThird()
    {
      return $this->getData('mobile_image_triple_third');

    }
    /**
    * function name : getReferenceUrlTripleThird
     * Retrieve url
     *
    */
    public function getReferenceUrlTripleThird()
    {
      return $this->getData('imageurl_triple_third');

    }
    /**
    * function name : getClassNameQuadruple
     * Retrieve classname
     *
    */
    //quadruple first image
    public function getClassNameQuadruple()
    {
      return $this->getData('class_name_quadruple');

    }
    /**
    * function name : getDesktopImagequadrupleFirst
     * Retrieve desktop image
     *
    */
    public function getDesktopImagequadrupleFirst()
    {
      return $this->getData('desktop_image_quadruple_first');

    }
    /**
    * function name : getMobileImagequadrupleFirst
     * Retrieve mobile image
     *
    */
    public function getMobileImagequadrupleFirst()
    {
      return $this->getData('mobile_image_quadruple_first');

    }
    /**
    * function name : getReferenceUrlquadrupleFirst
     * Retrieve url
     *
    */
    public function getReferenceUrlquadrupleFirst()
    {
      return $this->getData('imageurl_quadruple_first');

    }
    /**
    * function name : getDesktopImagequadrupleSecond
     * Retrieve desktop image
     *
    */
    //quadruple second image
    public function getDesktopImagequadrupleSecond()
    {
      return $this->getData('desktop_image_quadruple_second');

    }
    /**
    * function name : getMobileImagequadrupleSecond
     * Retrieve mobile image
     *
    */
    public function getMobileImagequadrupleSecond()
    {
      return $this->getData('mobile_image_quadruple_second');

    }
    /**
    * function name : getReferenceUrlquadrupleSecond
     * Retrieve url
     *
    */
    public function getReferenceUrlquadrupleSecond()
    {
      return $this->getData('imageurl_quadruple_second');

    }
    /**
    * function name : getDesktopImagequadrupleThird
     * Retrieve desktop image
     *
    */
    //quadruple third image
    public function getDesktopImagequadrupleThird()
    {
      return $this->getData('desktop_image_quadruple_third');

    }
    /**
    * function name : getMobileImagequadrupleThird
     * Retrieve mobile image
     *
    */
    public function getMobileImagequadrupleThird()
    {
      return $this->getData('mobile_image_quadruple_third');
    }
    /**
    * function name : getReferenceUrlquadrupleThird
     * Retrieve reference url
     *
    */
    public function getReferenceUrlquadrupleThird()
    {
      return $this->getData('imageurl_quadruple_third');
    }
    /**
    * function name : getDesktopImagequadrupleFourth
     * Retrieve desktop image
     *
    */
    //quadruple fourth image
    public function getDesktopImagequadrupleFourth()
    {
      return $this->getData('desktop_image_quadruple_fourth');
    }
    /**
    * function name : getMobileImagequadrupleFourth
     * Retrieve mobile image
     *
    */
    public function getMobileImagequadrupleFourth()
    {
      return $this->getData('mobile_image_quadruple_fourth');
    }
    /**
    * function name : getReferenceUrlquadrupleFourth
     * Retrieve reference url
     *
    */
    public function getReferenceUrlquadrupleFourth()
    {
      return $this->getData('imageurl_quadruple_fourth');
    }
}
