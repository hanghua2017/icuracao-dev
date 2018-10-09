<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\ResourceModel;

use Aheadworks\StoreLocator\Helper\Image;
use Aheadworks\StoreLocator\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Location.
 */
class Location extends AbstractDb
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @param Context $context
     * @param Image $imageHelper
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        $connectionName = null
    ) {
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $connectionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_storelocator_location', 'location_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);

        $this->saveStores($object);

        $this->updateImageField(
            $object->getId(),
            'image',
            $object->getData('image_additional_data')
        );
        $this->updateImageField(
            $object->getId(),
            'custom_marker',
            $object->getData('custom_marker_additional_data')
        );

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function saveStores(AbstractModel $object)
    {
        $this->getConnection()->delete(
            $this->getTable('aw_storelocator_location_store'),
            ['location_id = ?' => $object->getId()]
        );

        foreach ((array)$object->getData('stores') as $storeId) {
            $storeArray = [
                'location_id' => $object->getId(),
                'store_id' => $storeId
            ];

            $this->getConnection()->insert($this->getTable('aw_storelocator_location_store'), $storeArray);
        }

        return $this;
    }

    /**
     * @param int $locationId
     * @param string $field
     * @param string $value
     * @return $this
     * @throws LocalizedException
     */
    protected function updateImageField($locationId, $field, $value)
    {
        if (!$value) {
            return $this;
        }

        $uploadedImage = $this->imageHelper->processImageMedia(
            $field,
            $value,
            Config::AHEADWORKS_STORE_LOCATOR_LOCATION_DIRECTORY
        );

        $this->getConnection()->update(
            $this->getMainTable(),
            [$field => $uploadedImage],
            ['location_id = ?' => (int)$locationId]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->assignStores($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    protected function assignStores($object)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('aw_storelocator_location_store'), ['store_id'])
            ->where('location_id = :location_id');

        $stores = $this->getConnection()->fetchCol($select, [':location_id' => $object->getId()]);

        if ($stores) {
            $object->setData('stores', $stores);
        }
    }
}
