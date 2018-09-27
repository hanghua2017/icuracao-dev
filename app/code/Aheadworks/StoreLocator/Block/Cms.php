<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block;

use Aheadworks\StoreLocator\Helper\Config;
use Magento\Cms\Block\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Cms.
 */
class Cms extends Template
{
    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @param Context $context
     * @param Config $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $cmsBlockId = $this->helperConfig->getCmsBlock();

        $cmsBlock = $this->getLayout()->getBlock('aw_store_locator_cms');
        if ($cmsBlock && $cmsBlockId) {
            $cmsBlock->setCmsBlockHtml(
                $this->getLayout()->createBlock(Block::class)->setBlockId($cmsBlockId)->toHtml()
            );
        }

        return parent::_prepareLayout();
    }
}
