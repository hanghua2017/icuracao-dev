<?php

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
        /** @var \Quinoid\HomepageBanner\Model\Video $video */
        $model = $this->_coreRegistry->registry('dyode_dyode_form_data');

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

        if ($form->getId()) {
            $fieldset->addField(
                'form_id',
                'hidden',
                ['name' => 'form_id']
            );
        }
        $fieldset->addField(
          'first_name',
          'label', [
          'name' => 'first_name',
          'label' => 'First Name',
          ]
          );

          $fieldset->addField(
          'last_name',
          'label', [
          'name' => 'last_name',
          'label' => 'Last Name',
          ]
          );

          $fieldset->addField(
          'email',
          'label', [
          'name' => 'email',
          'label' => 'Email',
          ]
          );

          $fieldset->addField(
          'phonenumber',
          'label', [
          'name' => 'phonenumber',
          'label' => 'Phonenumber',
          ]
          );
          $fieldset->addField(
          'account_number',
          'label', [
          'name' => 'account_number',
          'label' => 'Account Number',
          ]
          );
          $fieldset->addField(
          'invoice_number',
          'label', [
          'name' => 'invoice_number',
          'label' => 'Invoice',
          ]
          );
          $fieldset->addField(
          'product_url',
          'label', [
          'name' => 'product_url',
          'label' => 'Url',
          ]
          );
          $fieldset->addField(
          'product_image_url',
          'label', [
          'name' => 'product_image_url',
          'label' => 'Image',
          ]
          );

        $form->setValues($model->getData());
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
