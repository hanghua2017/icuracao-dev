<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            F+QH6f8gWEYP7MDThQK5sY3nVEIJTKrEeZ4at/WUMj4=
 * Last Modified: 2018-09-29T09:26:41+00:00
 * File:          app/code/Xtento/ProductExport/Setup/UpgradeSchema.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.3.9', '<')) {
            $connection->addIndex(
                $setup->getTable('xtento_productexport_profile_history'),
                $setup->getIdxName('xtento_productexport_profile_history', ['entity_id']),
                ['entity_id']
            );
        }
        if (version_compare($context->getVersion(), '2.7.3', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_productexport_profile'),
                'category_mapping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => 16777215,
                    'comment' => 'Category Mapping'
                ]
            );
        }
        if (version_compare($context->getVersion(), '2.7.4', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_productexport_profile'),
                'taxonomy_source',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => 255,
                    'comment' => 'Taxonomy Source'
                ]
            );
        }

        $setup->endSetup();
    }
}
