<?php


namespace Dyode\Catalog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.4", "<")) {
            $this->upgradeSchemaOneZeroThree($setup);
        }
    }

    public function upgradeSchemaOneZeroThree(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $quoteItemTableName = $setup->getTable('quote_item');
            $orderItemTableName = $setup->getTable('sales_order_item');
        $connection = $setup->getConnection();

        if ($setup->getConnection()->isTableExists($quoteItemTableName) == true) {
            $columns = [
                'warranty_parent_item_id'   => [
                    'type'     => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment'  => 'Warranty Parent Item Relation',
                ],
            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($quoteItemTableName, $name, $definition);
            }
        }

        if ($setup->getConnection()->isTableExists($orderItemTableName) == true) {
            $columns = [
                'warranty_order_item_id'   => [
                    'type'     => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment'  => 'Warranty Order Item Relation',
                ],
            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($orderItemTableName, $name, $definition);
            }
        }


        $setup->endSetup();
    }
}