<?php
/**
* Navin Bhudiya
* Copyright (C) 2016 Navin Bhudiya <navindbhudiya@gmail.com>
*
* NOTICE OF LICENSE
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
*
* @category Navin
* @package Navin_Importexportcategory
* @copyright Copyright (c) 2016 Mage Delight (http://www.navinbhudiya.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Navin Bhudiya <navindbhudiya@gmail.com>
*/
namespace Navin\Importexportcategory\Block\Adminhtml\Exportcategory\Edit\Tab;

class Exportcategory extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storemanager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $url = $this->getViewFileUrl('Navin_Importexportcategory/navin_categoryimport.zip');
        $fieldsetexport = $form->addFieldset(
            'export_fieldset',
            [
                'legend' => __('Export Categories'),
                'class'  => 'fieldset-wide'
            ]
        );
        $_stores = [];
        $stores = $storemanager->getStores();
        foreach ($stores as $key => $store) {
            $_stores[$store->getId()] = $store->getName();
        }
        $fieldsetexport->addField(
            'store_id',
            'select',
            [
                'name'  => 'store_id',
                'label' => __('Export From Store'),
                'text' => __('Export From Store'),
                'values'  => $_stores
            ]
        );
        
        $_field = $fieldsetexport->addField(
            'export_all',
            'submit',
            [
                'name'  => 'export_all',
                'text' => __('Export All Categories'),
                'class' => 'action-default scalable save primary',
                'value'     => __('Export All Categories'),
                'style' => 'width:175px'
            ]
        );
              
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Sample Files'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'export_sample',
            'button',
            [
                'name'  => 'export_sample',
                'label' => __('Sample Files'),
                'text' => __('Sample Files'),
                'value'     => __('Sample Files'),
                'onclick'   => "javascript:window.location = '$url' ",
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Export Categories');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
