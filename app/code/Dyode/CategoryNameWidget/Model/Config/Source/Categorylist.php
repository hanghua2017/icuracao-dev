<?php
namespace Dyode\CategoryNameWidget\Model\Config\Source;
/**
 * @category Dyode
 * @package  Dyode_CategoryNameWidget
 * @module   CategoryNameWidget
 * @author  Nismath V I
 */
use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class Categorylist implements ArrayInterface
{
  protected $_categoryHelper;
  public function __construct(\Magento\Catalog\Helper\Category $catalogCategory)
  {
    $this->_categoryHelper = $catalogCategory;
  }
    /*
     * Return categories helper
     */

    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    /*
    * function name : toOptionArray
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {


        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {

            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /*
    * function name : toArray
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {

        $categories = $this->getStoreCategories(true,false,true);

        $catagoryList = array();
        foreach ($categories as $category){

            $catagoryList[$category->getEntityId()] = __($category->getName());
        }

        return $catagoryList;
    }

}
?>