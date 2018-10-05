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
      $columns = [
          'delivery_type' => [
              'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              'nullable' => true,
              'default' => '0',
              'comment'=>'type of delivery'
          ],
          'pickup_location_address' => [
              'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              'nullable' => true,
              'comment'=>'address of delivery'
          ],
          'pickup_location' => [
              'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              'nullable' => true,
              'comment'=>'location id'
           ]
      ];
      if (version_compare($context->getVersion(), "2.0.1", "<")) {
      //Your upgrade script
      }
      if (version_compare($context->getVersion(), '2.0.2', '<')) {
        // $installer->getConnection()->addColumn(
        //       $installer->getTable('sales_order_item'),
        //       $columns
        //   );
          $orderTable =   $installer->getTable('sales_order_item');
          $connection = $installer->getConnection();
          foreach ($columns as $name => $definition) {
              $connection->addColumn($orderTable, $name, $definition);
          }
      }
      $installer->endSetup();
    }
}
