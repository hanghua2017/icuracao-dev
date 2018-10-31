<?php
/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Extending Magento_Order
 *
 * @package   Dyode
 * @module    Dyode_CheckoutDeliveryMethod
 * @author    kavitha@dyode.com
 */

namespace Dyode\Checkout\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeSchemaTwoZeroOne($installer);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgradeSchemaTwoZeroFive($installer);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgradeSchemaTwoZeroFive($installer);
        }

        $installer->endSetup();
    }

    /**
     * Schema for 2.0.2
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return $this
     */
    public function upgradeSchemaTwoZeroOne(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        // Updating the 'catalog_product_bundle_option_value' table.
        $connection->addColumn(
            $installer->getTable('quote_item'),
            'shipping_details',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'comment' => 'Shipping details',
            ]
        );

        return $this;
    }

    /**
     * Remove unwanted columns from sales_order and quote tables.
     * Remove unwanted columns from sales_order_item and quote_item tables.
     * Add new columns into sales_order table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @return $this
     */
    public function upgradeSchemaTwoZeroFive(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $salesOrderTable = $installer->getTable('sales_order');
        $quoteTable = $installer->getTable('quote');
        $salesOrderItemTable = $installer->getTable('sales_order_item');
        $quoteItemTable = $installer->getTable('quote_item');

        //Remove columns from quote and sales_order tables.
        $columnToRemove = ['use_credit', 'curacaocredit_used'];
        foreach ($columnToRemove as $column) {
            if ($connection->tableColumnExists($salesOrderTable, $column)) {
                $connection->dropColumn($salesOrderTable, $column);
            }

            if ($connection->tableColumnExists($quoteTable, $column)) {
                $connection->dropColumn($quoteTable, $column);
            }
        }

        //Remove columns from quote_item and sales_order_item tables.
        $columnToRemove = ['shipping_cost', 'pickup_location_address'];
        foreach ($columnToRemove as $column) {
            if ($connection->tableColumnExists($quoteItemTable, $column)) {
                $connection->dropColumn($quoteItemTable, $column);
            }

            if ($connection->tableColumnExists($salesOrderItemTable, $column)) {
                $connection->dropColumn($salesOrderItemTable, $column);
            }
        }

        //Add columns into sales_order table.
        $columnsToAdd = [
            'is_curacao_credit_used' => [
                'type'     => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'unsigned' => true,
                'default'  => 0,
                'comment'  => 'Indicate whether the order payment involves curacao credit payment.',
            ],
            'curacao_down_payment'   => [
                'type'      => Table::TYPE_DECIMAL,
                'nullable'  => true,
                'precision' => 12,
                'scale'     => 4,
                'unsigned'  => true,
                'comment'   => 'Curacao down payment involved in the order.',
            ],
        ];
        foreach ($columnsToAdd as $columnName => $columnDefinition) {
            $connection->addColumn($salesOrderTable, $columnName, $columnDefinition);
        }

        return $this;

    }
}
