<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Class Menu.
 */
class Menu extends Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename.
     *
     * @var string
     */
    protected $_template = 'Aheadworks_StoreLocator::page/menu.phtml';

    /**
     * @var string
     */
    protected $className = 'aw-modulename-menu';

    /**
     * Get menu container class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array|null
     */
    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                'location' => [
                    'title' => __('Locations'),
                    'url' => $this->getUrl('*/location/index'),
                    'resource' => 'Aheadworks_StoreLocator::location'
                ],
                'system_config' => [
                    'title' => __('Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'aw_store_locator'])
                ],
                'readme' => [
                    'title' => __('Readme'),
                    'url' => 'http://confluence.aheadworks.com/display/EUDOC/Store+Locator+-+Magento+2',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'http://ecommerce.aheadworks.com/contacts/',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]
            ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }
        return $this->items;
    }

    /**
     * @return string
     */
    public function getCurrentItemTitle()
    {
        $items = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName]['title'];
        }
        return '';
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }
        return $result;
    }

    /**
     * @param string $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }
}
