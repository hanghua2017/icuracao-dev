<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       23/08/2018
 */

namespace Dyode\Checkout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface
{

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
public function install (SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $installer = $setup;

    $installer->startSetup();

    $eavTable = $installer->getTable('quote');

    $columns = [
        'use_credit' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => '1',
            'comment' => 'credit balance',
        ],
        'curacaocredit_used' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'credit used',
        ],
    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($eavTable, $name, $definition);
    }

    $installer->endSetup();
}
}
?>
