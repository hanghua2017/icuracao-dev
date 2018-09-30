<?php

namespace Dyode\CategoryBanner\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.0') < 0){





		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'categorydesktopbanner');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'categorydesktopbanner', [
                        'type' => 'varchar',
                        'label' => 'Category Banner (Desktop)',
                        'input' => 'image',
                        'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
						'required' => false,
                        'sort_order' => 110,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'categorymobilebanner');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'categorymobilebanner', [
                        'type' => 'varchar',
                        'label' => 'Category Banner (Mobile)',
                        'input' => 'image',
                        'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
						'required' => false,
                        'sort_order' => 120,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'categorybannerlink');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'categorybannerlink', [
                        'type' => 'varchar',
                        'label' => 'Category Banner Link',
                        'input' => 'text',
						'required' => false,
                        'sort_order' => 130,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'group' => 'General Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	



		}

    }
}