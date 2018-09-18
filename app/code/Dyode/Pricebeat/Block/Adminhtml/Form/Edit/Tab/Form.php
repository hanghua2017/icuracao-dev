<?php
/**
 * Dyode_Pricebeat extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Dyode
 *                     @package   Dyode_Pricebeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Pricebeat\Block\Adminhtml\Form\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $booleanOptions;

    /**
     * constructor
     *
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->booleanOptions = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Dyode\Pricebeat\Model\Form $form */
        $form = $this->_coreRegistry->registry('dyode_pricebeat_form');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addType('image', 'Dyode\Pricebeat\Block\Adminhtml\Form\Helper\Image');
        $fieldset->addType('file', 'Dyode\Pricebeat\Block\Adminhtml\Form\Helper\File');
        if ($form->getId()) {
            $fieldset->addField(
                'form_id',
                'hidden',
                ['name' => 'form_id']
            );
        }
        $fieldset->addField(
            'first_name',
            'text',
            [
                'name'  => 'first_name',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'last_name',
            'text',
            [
                'name'  => 'last_name',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'phonenumber',
            'integer',
            [
                'name'  => 'phonenumber',
                'label' => __('Phonenumber'),
                'title' => __('Phonenumber'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'account_number',
            'integer',
            [
                'name'  => 'account_number',
                'label' => __('Accountnumber'),
                'title' => __('Accountnumber'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'invoice_number',
            'integer',
            [
                'name'  => 'invoice_number',
                'label' => __('Invoicenumber'),
                'title' => __('Invoicenumber'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'redirect_url',
            'text',
            [
                'name'  => 'redirect_url',
                'label' => __('Redirect URL'),
                'title' => __('Redirect URL'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'show_in_frontend',
            'select',
            [
                'name'  => 'show_in_frontend',
                'label' => __('Show in Frontend'),
                'title' => __('Show in Frontend'),
                'required' => true,
                'values' => $this->booleanOptions->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $this->booleanOptions->toOptionArray(),
            ]
        );


        $fieldset->addField(
            'imagethumbnail',
            'file',
            [
                'name'  => 'imagethumbnail',
                'label' => __('Responsive Image File'),
                'title' => __('Responsive Image File'),
            ]
        );
        $fieldset->addField(
            'product_image_url',
            'text',
            [
                'name'  => 'product_image_url',
                'label' => __('Form URL'),
                'title' => __('Form URL'),
            ]
        );
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )


            ->addFieldMap(
                "{$htmlIdPrefix}imagethumbnail",
                'imagethumbnail'
            )

            ->addFieldMap(
                "{$htmlIdPrefix}product_image_url",
                'product_image_url'
            )

        );
        $formData = $this->_session->getData('Dyode_pricebeat_form_data', true);
        if ($formData) {
            $form->addData($formData);
        } else {
            if (!$form->getId()) {
                $form->addData($form->getDefaultValues());
            }
        }
        $form->addValues($form->getData());
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
        return __('Pricebeat');
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
