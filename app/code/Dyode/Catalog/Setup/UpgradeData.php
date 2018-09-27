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
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->upgradeDataOneZeroTwo($setup);
        }
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