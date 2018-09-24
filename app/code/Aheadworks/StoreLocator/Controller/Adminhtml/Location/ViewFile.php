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
use Aheadworks\StoreLocator\Model\Config;
use Aheadworks\StoreLocator\Model\Location;
use Aheadworks\StoreLocator\Model\LocationFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\LayoutFactory as ResultLayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Helper\File\Storage;

/**
 * Class ViewFile.
 */
class ViewFile extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_StoreLocator::location';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Storage
     */
    protected $fileStorage;

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
     * @param RawFactory $resultRawFactory
     * @param DecoderInterface $urlDecoder
     * @param Filesystem $fileSystem
     * @param Storage $fileStorage
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
        JsonFactory $resultJsonFactory,
        RawFactory $resultRawFactory,
        DecoderInterface $urlDecoder,
        Filesystem $fileSystem,
        Storage $fileStorage
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $locationFactory,
            $locationModel,
            $locationRepository,
            $locationDataFactory,
            $locationImageDataFactory,
            $locationManagement,
            $dataObjectHelper,
            $layoutFactory,
            $resultLayoutFactory,
            $resultPageFactory,
            $resultForwardFactory,
            $resultJsonFactory
        );
        $this->resultRawFactory = $resultRawFactory;
        $this->urlDecoder  = $urlDecoder;
        $this->fileSystem  = $fileSystem;
        $this->fileStorage  = $fileStorage;
    }

    /**
     * @return Raw
     * @throws NotFoundException
     * @throws FileSystemException
     */
    public function execute()
    {
        $locationId = $this->getRequest()->getParam('location_id', null);

        $file = null;
        if ($this->getRequest()->getParam('file') && $locationId) {
            $file = $this->urlDecoder->decode(
                $this->getRequest()->getParam('file')
            );
        } else {
            throw new NotFoundException(__('Page not found.'));
        }

        $directory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = Config::AHEADWORKS_STORE_LOCATOR_MEDIA_PATH .
            DIRECTORY_SEPARATOR .
            ltrim($file, DIRECTORY_SEPARATOR);

        $path = $directory->getAbsolutePath($fileName);
        if (!$directory->isFile($fileName)
            && !$this->fileStorage->processStorageFile($path)
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        switch (strtolower($extension)) {
            case 'gif':
                $contentType = 'image/gif';
                break;
            case 'jpg':
                $contentType = 'image/jpeg';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
            default:
                $contentType = 'application/octet-stream';
                break;
        }
        $stat = $directory->stat($fileName);
        $contentLength = $stat['size'];
        $contentModify = $stat['mtime'];

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Last-Modified', date('r', $contentModify));
        $resultRaw->setContents($directory->readFile($fileName));
        return $resultRaw;
    }
}
