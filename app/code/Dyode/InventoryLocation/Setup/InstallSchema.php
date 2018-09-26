<?php 
namespace Dyode\InventoryLocation\Setup;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface{
    public function install(SchemaSetupInterface $setup,ModuleContextInterface $context){
        $setup->startSetup();
        $conn = $setup->getConnection();
        $tableName = $setup->getTable('location_inventory');
        if($conn->isTableExists($tableName) != true){
            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'id',
                                Table::TYPE_INTEGER,
                                null,
                                ['identity'=>true,'unsigned'=>true,'nullable'=>false,'primary'=>true]
                                )
                            ->addColumn(
                                'productid',
                                Table::TYPE_INTEGER,
                                10,
                                ['nullable'=>false]
                                )
                            ->addColumn(
                                'productsku',
                                Table::TYPE_TEXT,
                                255,
                                ['nullable'=>false]
                                )
                            ->addColumn(
                                'inventory',
                                Table::TYPE_TEXT,
                                '2M',
                                ['nullbale'=>true,'default'=>'']
                                )
                            ->setOption('charset','utf8');
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}
?>
