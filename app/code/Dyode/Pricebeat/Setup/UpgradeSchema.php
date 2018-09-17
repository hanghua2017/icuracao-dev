<?php
namespace Dyode\Pricebeat\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
      $installer = $setup;

      $installer->startSetup();
      if (version_compare($context->getVersion(), "1.0.0", "<")) {
      //Your upgrade script
      }
      if (version_compare($context->getVersion(), '1.0.1', '<')) {
        $installer->getConnection()->addColumn(
              $installer->getTable('dyode_pricebeat_form'),
              'redirect_url',
              [
                  'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  'length' => 255,
                  'nullable' => false,
                  'comment' => 'Redirect URL'
              ]
          );
      }
      $installer->endSetup();
    }
}
