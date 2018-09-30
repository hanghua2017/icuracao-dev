<?php
/**
 * Copyright © Dyode, Inc. All rights reserved.
 */
namespace Dyode\PriceUpdate\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * InstallData
 * @category Dyode
 * @package  Dyode_PriceUpdate
 * @module   PriceUpdate
 * @author   Nithin
 */
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
	  * constructor function
	 */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

	 /**
	  * function name : install
	  * definition : install price attributes
	  * @return no return
	 */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup -> removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'vendor_rebate');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'vendor_rebate',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Vendor Rebate',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );
        $eavSetup -> removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'customer_rebate');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'customer_rebate',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Customer Rebate',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }
}
