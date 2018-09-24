<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'aw_storelocator_location'.
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aw_storelocator_location')
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Location ID'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Location Title'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Location Description'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => '0'],
            'Location Status'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => '0'],
            'Location Sort Order'
        )->addColumn(
            'country_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Country'
        )->addColumn(
            'region_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Region/State'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location City'
        )->addColumn(
            'street',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Street'
        )->addColumn(
            'zip',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Zip'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Phone'
        )->addColumn(
            'zoom',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Location Zoom'
        )->addColumn(
            'latitude',
            Table::TYPE_FLOAT,
            '10,6',
            ['nullable' => false, 'default' => '0'],
            'Location Latitude'
        )->addColumn(
            'longitude',
            Table::TYPE_FLOAT,
            '10,6',
            ['nullable' => false, 'default' => '0'],
            'Location Longitude'
        )->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Image'
        )->addColumn(
            'custom_marker',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Youtube Custom Marker'
        )->setComment(
            'Aheadworks Store Locator - Location table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_storelocator_location_store'.
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aw_storelocator_location_store')
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Location Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
        )->addIndex(
            $installer->getIdxName('aw_storelocator_location_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                'aw_storelocator_location_store',
                'location_id',
                'aw_storelocator_location',
                'location_id'
            ),
            'location_id',
            $installer->getTable('aw_storelocator_location'),
            'location_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'aw_storelocator_location_store',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Aheadworks Store Locator - Location to Store table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
