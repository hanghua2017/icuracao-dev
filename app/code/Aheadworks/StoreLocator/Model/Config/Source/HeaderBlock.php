<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;

/**
 * Class HeaderBlock.
 */
class HeaderBlock implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $blockCollectionFactory;

    /**
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(
        CollectionFactory $blockCollectionFactory
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = $this->blockCollectionFactory->create()->load()->toOptionArray();
        array_unshift($options, ['value' => '', 'label' => __('Please select a static block.')]);
        return $options;
    }
}
