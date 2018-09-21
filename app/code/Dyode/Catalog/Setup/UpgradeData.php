<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Setup;

use Dyode\Catalog\Model\Product\Link;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $this->upgradeDataOneZeroOne($setup);
        }
    }

    /**
     * Create "warranty" relation for catalog product entity.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function upgradeDataOneZeroOne(ModuleDataSetupInterface $setup)
    {
        $data = [
            [
                'link_type_id' => Link::LINK_TYPE_WARRANTY,
                'code'         => Link::LINK_WARRANTY_CODE,
            ],
        ];

        foreach ($data as $bind) {
            $setup->getConnection()
                ->insertForce($setup->getTable('catalog_product_link_type'), $bind);
        }

        $data = [
            [
                'link_type_id'                => Link::LINK_TYPE_WARRANTY,
                'product_link_attribute_code' => 'position',
                'data_type'                   => 'int',
            ],
        ];

        $setup->getConnection()
            ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
    }
}