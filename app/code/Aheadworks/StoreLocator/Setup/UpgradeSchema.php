<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema.
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $this->alterGoogleMapsCoordinateFields($setup);
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $this->addCustomStoreFields($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function alterGoogleMapsCoordinateFields(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $locationTable = $setup->getTable('aw_storelocator_location');

        $connection->modifyColumn(
            $locationTable,
            'zoom',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Location Zoom',
            ]
        );

        $connection->modifyColumn(
            $locationTable,
            'latitude',
            [
                'type' => Table::TYPE_FLOAT,
                'size' => '10,6',
                'nullable' => false,
                'default' => '0',
                'comment' => 'Location Latitude',
            ]
        );

        $connection->modifyColumn(
            $locationTable,
            'longitude',
            [
                'type' => Table::TYPE_FLOAT,
                'size' => '10,6',
                'nullable' => false,
                'default' => '0',
                'comment' => 'Location Longitude',
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addCustomStoreFields(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $locationTable = $setup->getTable('aw_storelocator_location');

        $connection->addColumn(
            $locationTable,
            'store_location_code',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'unsigned' => true,
                'comment' => 'Store Location Code',
            ]
        );

        $connection->addColumn(
            $locationTable,
            'store_manager',
            [
                'type' => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable' => false,
                'comment' => 'Store Manager',
            ]
        );

        $connection->addColumn(
            $locationTable,
            'store_email',
            [
                'type' => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable' => false,
                'comment' => 'Store Email',
            ]
        );

        $connection->addColumn(
            $locationTable,
            'fax',
            [
                'type' => Table::TYPE_TEXT,
                'length'    => 255,
                'nullable' => false,
                'comment' => 'Location Fax',
            ]
        );
    }
}
