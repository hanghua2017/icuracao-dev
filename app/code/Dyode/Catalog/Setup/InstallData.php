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
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /**
         * Install product link type: fbt
         */
        $data = [
            [
                'link_type_id' => Link::LINK_TYPE_FBT,
                'code'         => Link::LINK_CODE,
            ],
        ];

        foreach ($data as $bind) {
            $setup->getConnection()
                ->insertForce($setup->getTable('catalog_product_link_type'), $bind);
        }

        /**
         * install product link attributes
         */
        $data = [
            [
                'link_type_id'                => Link::LINK_TYPE_FBT,
                'product_link_attribute_code' => 'position',
                'data_type'                   => 'int',
            ],
        ];

        $setup->getConnection()
            ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
    }
}