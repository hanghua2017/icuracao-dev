<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Observer;

use Aheadworks\StoreLocator\Controller\Router;
use Aheadworks\StoreLocator\Helper\Config;
use Aheadworks\StoreLocator\Model\Config\Source\TopMenu;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

class AddItemToTopmenuItemsObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @param UrlInterface $url
     * @param Config $helperConfig
     */
    public function __construct(
        UrlInterface $url,
        Config $helperConfig
    ) {
        $this->url = $url;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Add store locator link to top menu.
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $moduleName = $observer->getEvent()->getBlock()->getRequest()->getModuleName();
        $this->addStoreLocatorToMenu($observer->getMenu(), $moduleName);
    }

    /**
     * Add store locator link to top menu.
     *
     * @param Node $parentNode
     * @param string $moduleName
     * @return void
     */
    protected function addStoreLocatorToMenu($parentNode, $moduleName)
    {
        $storeLocatorMenuItem = $this->helperConfig->getTopMenuItem();
        if (!$storeLocatorMenuItem) {
            return;
        }

        $tree = $parentNode->getTree();

        $storeLocatorData = $this->getMenuStoreLocatorData($moduleName);
        $node = new Node($storeLocatorData, 'id', $tree, $parentNode);

        $menuItems = [];
        $menuItems[] = $node;

        if ($storeLocatorMenuItem == TopMenu::MENU_ITEM_LEFT_ALIGN) {
            foreach ($parentNode->getChildren() as $child) {
                $menuItems[] = $child;
                $parentNode->removeChild($child);
            }
        }

        foreach ($menuItems as $child) {
            $parentNode->addChild($child);
        }
    }

    /**
     * Get store locator data to be added to the menu.
     *
     * @param string $moduleName
     * @return array
     */
    public function getMenuStoreLocatorData($moduleName)
    {
        $nodeId = 'store-locator-node';

        $isActiveCategory = false;

        if ($moduleName == Router::MODULE_NAME) {
            $isActiveCategory = true;
        }

        $name = $this->helperConfig->getTitle();
        $url = $this->helperConfig->getUrlKey();
        $storeLocatorData = [
            'name' => $name,
            'id' => $nodeId,
            'url' => $this->url->getUrl($url),
            'has_active' => false,
            'is_active' => $isActiveCategory
        ];

        return $storeLocatorData;
    }
}
