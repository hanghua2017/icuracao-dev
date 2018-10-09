<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller;

use Aheadworks\StoreLocator\Helper\Config;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Url;

/**
 * Class Router.
 */
class Router implements RouterInterface
{
    /**
     * Module name.
     */
    const MODULE_NAME = 'aw_store_locator';

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @param ActionFactory $actionFactory
     * @param Config $helperConfig
     * @param Manager $moduleManager
     */
    public function __construct(
        ActionFactory $actionFactory,
        Config $helperConfig,
        Manager $moduleManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->helperConfig = $helperConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Validate and Match Store Locator Page and modify request.
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        if (!$this->moduleManager->isOutputEnabled('Aheadworks_StoreLocator')) {
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $isStoreLocatorPageEnabled = $this->helperConfig->isStoreLocatorPageEnabled();
        if (!$isStoreLocatorPageEnabled) {
            return null;
        }

        $urlKey = $this->helperConfig->getUrlKey();
        if ($identifier != $urlKey) {
            return null;
        }

        $request->setModuleName(self::MODULE_NAME)->setControllerName('index')->setActionName('index');
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }
}
