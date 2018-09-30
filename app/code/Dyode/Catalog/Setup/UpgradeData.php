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

use Dyode\Catalog\Model\Product\Attribute\Source\WarrantyCmsBlock;
use Dyode\Catalog\Model\Product\Link;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Dyode\Catalog\Model\Product\Attribute\Source\WarrantyCmsBlock
     */
    private $warrantyCmsBlock;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Eav\Setup\EavSetupFactory                             $eavSetupFactory
     * @param \Dyode\Catalog\Model\Product\Attribute\Source\WarrantyCmsBlock $warrantyCmsBlock
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        WarrantyCmsBlock $warrantyCmsBlock
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->warrantyCmsBlock = $warrantyCmsBlock;
    }


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
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->upgradeDataOneZeroTwo($setup);
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

    /**
     * Add "warranty_cms_block" product attribute.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function upgradeDataOneZeroTwo(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'warranty_cms_block',
            [
                'type'                    => 'int',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Warranty CMS Block',
                'input'                   => 'select',
                'class'                   => '',
                'source'                  => WarrantyCmsBlock::class,
                'global'                  => 1,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => true,
                'default'                 => null,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => false,
                'unique'                  => false,
                'apply_to'                => 'simple,grouped,bundle,configurable',
                'system'                  => 0,
                'group'                   => 'General',
            ]
        );
    }
}