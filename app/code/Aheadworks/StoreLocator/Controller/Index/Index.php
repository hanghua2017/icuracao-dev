<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Index;

use Aheadworks\StoreLocator\Helper\Config;
use Aheadworks\StoreLocator\Model\Location;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index.
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Location
     */
    protected $locationModel;

    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Location $locationModel
     * @param Config $helperConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Location $locationModel,
        Config $helperConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->locationModel = $locationModel;
        $this->helperConfig = $helperConfig;
        parent::__construct($context);
    }

    /**
     * Default page
     *
     * @return Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $searchPostData = [];
        if ($postData) {
            $searchPostData = $postData['search'];
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->initMessages();
        $resultPage->getConfig()->getTitle()->set($this->helperConfig->getTitle());

        if (isset($searchPostData)) {
            $resultPage->getLayout()->getBlock('aw_store_locator_search')->setData($searchPostData);
        }

        $collection = $this->locationModel->getLocationCollectionBySearch($searchPostData);

        $locationBlock = $resultPage->getLayout()->getBlock('aw_store_locator_location');
        if ($locationBlock) {
            $locationBlock->setCollection($collection);
        }

        $mapBlock = $resultPage->getLayout()->getBlock('aw_store_locator_map');
        if ($mapBlock) {
            $mapBlock->setCollection($collection);
        }

        return $resultPage;
    }
}
