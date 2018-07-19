<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Aheadworks\StoreLocator\Api\LocationManagementInterface;
use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory;
use Aheadworks\StoreLocator\Api\Data\LocationImageInterfaceFactory;
use Aheadworks\StoreLocator\Controller\RegistryConstants;
use Aheadworks\StoreLocator\Model\Location;
use Aheadworks\StoreLocator\Model\LocationFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\Error;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\LayoutFactory as ResultLayoutFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index.
 */
abstract class AbstractIndex extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var LocationFactory
     */
    protected $locationFactory = null;

    /**
     * @var Location
     */
    protected $locationModel = null;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var LocationInterfaceFactory
     */
    protected $locationDataFactory;

    /**
     * @var LocationImageInterfaceFactory
     */
    protected $locationImageDataFactory;

    /**
     * @var LocationManagementInterface
     */
    protected $locationManagement;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var ResultLayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LocationFactory $locationFactory
     * @param Location $locationModel
     * @param LocationRepositoryInterface $locationRepository
     * @param LocationInterfaceFactory $locationDataFactory
     * @param LocationImageInterfaceFactory $locationImageDataFactory
     * @param LocationManagementInterface $locationManagement
     * @param DataObjectHelper $dataObjectHelper
     * @param LayoutFactory $layoutFactory
     * @param ResultLayoutFactory $resultLayoutFactory
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LocationFactory $locationFactory,
        Location $locationModel,
        LocationRepositoryInterface $locationRepository,
        LocationInterfaceFactory $locationDataFactory,
        LocationImageInterfaceFactory $locationImageDataFactory,
        LocationManagementInterface $locationManagement,
        DataObjectHelper $dataObjectHelper,
        LayoutFactory $layoutFactory,
        ResultLayoutFactory $resultLayoutFactory,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->locationFactory = $locationFactory;
        $this->locationModel = $locationModel;
        $this->locationRepository = $locationRepository;
        $this->locationDataFactory = $locationDataFactory;
        $this->locationImageDataFactory = $locationImageDataFactory;
        $this->locationManagement = $locationManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Location initialization.
     *
     * @param string $idFieldName
     * @return string location id
     */
    protected function initLocation($idFieldName = 'location_id')
    {
        $locationId = (int)$this->getRequest()->getParam($idFieldName);

        if ($locationId) {
            $this->coreRegistry->register(RegistryConstants::CURRENT_LOCATION_ID, $locationId);
        }

        return $locationId;
    }

    /**
     * Prepare location default title.
     *
     * @param Page $resultPage
     * @return void
     */
    protected function prepareDefaultLocationTitle(Page $resultPage)
    {
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator by Aheadworks'));
    }

    /**
     * Add errors messages to session.
     *
     * @param array|string $messages
     * @return void
     */
    protected function addSessionErrorMessages($messages)
    {
        $messages = (array)$messages;
        $session = $this->_getSession();

        $callback = function ($error) use ($session) {
            if (!$error instanceof Error) {
                $error = new Error($error);
            }
            $this->messageManager->addMessage($error);
        };
        array_walk_recursive($messages, $callback);
    }
}
