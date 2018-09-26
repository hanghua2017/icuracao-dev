<?php
/**
 *Dyode_Interestbeat extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Dyode
 *                     @package   Dyode_Interestbeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Interestbeat\Setup;

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
              $installer->getTable('dyode_interestbeat_form'),
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
