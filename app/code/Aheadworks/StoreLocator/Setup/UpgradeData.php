<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Setup;

use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData.
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Relation
     */
    private $relationProcessor;

    /**
     * @param Relation $relationProcessor
     */
    public function __construct(Relation $relationProcessor)
    {
        $this->relationProcessor = $relationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $this->updateLocationImagePath($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function updateLocationImagePath($setup)
    {
        $locationEntityTable = $setup->getTable('aw_storelocator_location');

        $select = $setup->getConnection()->select()->from(
            $locationEntityTable,
            ['location_id', 'image', 'custom_marker']
        );

        $locations = $setup->getConnection()->fetchAll($select);
        foreach ($locations as $location) {
            $bind = [
                'image' => $location['image'] === null ? null : $location['location_id'] .
                    DIRECTORY_SEPARATOR . 'image' .
                    DIRECTORY_SEPARATOR . $location['image'],
                'custom_marker' => $location['custom_marker'] === null ? null : $location['location_id'] .
                    DIRECTORY_SEPARATOR . 'custom_marker' .
                    DIRECTORY_SEPARATOR . $location['custom_marker']
            ];
            $where = ['location_id = ?' => (int)$location['location_id']];

            $setup->getConnection()->update($locationEntityTable, $bind, $where);
        }
    }
}
