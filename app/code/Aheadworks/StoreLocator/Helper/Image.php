<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Helper;

use Aheadworks\StoreLocator\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Image.
 */
class Image extends AbstractHelper
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve media URL.
     *
     * @return string
     */
    public function getBaseMedialPath()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Retrieve store locator media URL.
     *
     * @return string
     */
    public function getBaseStoreLocatorPath()
    {
        return $this->getBaseMedialPath() . Config::AHEADWORKS_STORE_LOCATOR_MEDIA_PATH;
    }

    /**
     * Retrieve store locator media image URL.
     *
     * @param string $file
     * @return string
     */
    public function getImagePath($file)
    {
        return $file ? $this->getBaseStoreLocatorPath() . DIRECTORY_SEPARATOR . $file : '';
    }

    /**
     * Process location images.
     *
     * @param string $fileId
     * @param string[] $value
     * @param string $folder
     * @return string
     */
    public function processImageMedia($fileId, $value, $folder)
    {
        if (empty($value)) {
            return false;
        }

        if (is_array($value) && !empty($value['delete'])) {
            return '';
        }

        $uploadedFile = $value['value'];

        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath(
                Config::AHEADWORKS_STORE_LOCATOR_MEDIA_PATH .
                DIRECTORY_SEPARATOR .
                $folder
            );

        try {
            /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
            $uploader = $this->fileUploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $uploader->save($path);

            $uploadedFile = $folder . $uploader->getUploadedFileName();
        } catch (\Exception $e) {
            if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                $this->_logger->critical($e);
            }
        }

        return $uploadedFile;
    }
}
