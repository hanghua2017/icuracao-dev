<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Model\Product\Attribute\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Source Model of 'warranty_cms_block' product attribute
 */
class WarrantyCmsBlock extends AbstractSource
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $cmsBlockCollection;

    /**
     * WarrantyCmsBlock constructor.
     *
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $cmsBlockCollection
     */
    public function __construct(CollectionFactory $cmsBlockCollection)
    {
        $this->cmsBlockCollection = $cmsBlockCollection;
    }

    /**
     * Provide cms blocks as the options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->options) {
            $defaultOption = [['value' => '', 'label' => __('Select a CMS block')]];
            $this->options = array_merge($defaultOption, $this->cmsBlockCollection->create()->toOptionArray());
        }
        return $this->options;
    }
}