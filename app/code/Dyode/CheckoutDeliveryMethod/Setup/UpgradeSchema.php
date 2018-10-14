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
namespace Dyode\CheckoutDeliveryMethod\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->upgradeSchemaTwoZeroTwo($installer);
        }

        $installer->endSetup();
    }

    /**
     * Schema for 2.0.2
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    public function upgradeSchemaTwoZeroTwo(SchemaSetupInterface $installer)
    {
        $columns = [
            'delivery_type'   => [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'default'  => '0',
                'comment'  => 'type of delivery',
            ],
            'pickup_location' => [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment'  => 'location id',
            ],
        ];
        $orderTable = $installer->getTable('sales_order_item');
        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($orderTable, $name, $definition);
        }
    }
}
