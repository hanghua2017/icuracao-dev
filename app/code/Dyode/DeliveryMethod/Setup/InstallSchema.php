<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       03/09/2018
 */

namespace Dyode\Delivery\Setup;

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

    $eavTable = $installer->getTable('quote_item');

    $columns = [
        'delivery_type' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => '1',
        ],
        'pickup_location_address' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
        ],
        'pickup_location' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'nullable' => true,
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