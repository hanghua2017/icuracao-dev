<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $tableName = $setup->getTable('catalog_product_option');

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'is_wall_mount' => [
                    'type'     => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default'  => '0',
                    'comment'  => 'Indicates whether the option is wall-mount type or not',
                ],
            ];

            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        $setup->endSetup();
    }
}