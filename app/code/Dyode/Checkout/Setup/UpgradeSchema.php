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

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeSchemaTwoZeroThree($installer);
        }

        $installer->endSetup();
    }

    /**
     * Schema for 2.0.2
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    public function upgradeSchemaTwoZeroOne(SchemaSetupInterface $installer)
    {
      $connection = $installer->getConnection();
      // Updating the 'catalog_product_bundle_option_value' table.
      $connection->addColumn(
          $installer->getTable('quote_item'),
          'shipping_details',
          [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'comment'  => 'Shipping details'
          ]
      );

      $connection->addColumn(
          $installer->getTable('sales_order_item'),
          'shipping_details',
          [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'comment'  => 'Shipping details'
          ]
      );
      $connection->addColumn(
          $installer->getTable('sales_order'),
            'use_credit',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => '1',
                'comment' => 'credit balance'
            ]
        );

        $connection->addColumn(
            $installer->getTable('sales_order'),
            'curacaocredit_used',
             [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'default' => '0.0000',
                'comment' => 'credit used',
            ]
          );
    }
    /*
    * Schema for 2.0.3
    *
    * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
    */
    public function upgradeSchemaTwoZeroThree(SchemaSetupInterface $installer)
    {
      $connection = $installer->getConnection();
      // Updating the 'quote_item' table.
      $connection->addColumn(
          $installer->getTable('quote_item'),
          'shipping_cost',
          [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'comment'  => 'Shipping Cost'
          ]
      );
    }
}
