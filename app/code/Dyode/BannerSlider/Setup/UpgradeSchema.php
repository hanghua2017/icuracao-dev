<?php
namespace Dyode\BannerSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade schema
 * @category Dyode
 * @package  Dyode_BannerSlider
 * @module   BannerSlider
 * @author   Nithin <nithin@dyode.com>
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * add bannerstore_id field
    */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
          $installer->getConnection()->addColumn(
                $installer->getTable('magestore_bannerslider_banner'),
                'bannerstore_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Store Id'

                ]
            );
        }
        $installer->endSetup();
    }
}
