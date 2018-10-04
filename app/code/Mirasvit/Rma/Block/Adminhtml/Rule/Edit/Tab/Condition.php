<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


//@codingStandardsIgnoreFile
namespace Mirasvit\Rma\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Condition extends Generic implements TabInterface
{
    protected $_nameInLayout = 'conditions_serialized';
    protected $formName = 'rule_form';

    public function __construct(
        \Mirasvit\Rma\Model\Config\Source\Rule\Event $configSourceRuleEvent,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->configSourceRuleEvent = $configSourceRuleEvent;
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->conditions = $conditions;
        $this->formFactory = $formFactory;
        $this->backendUrlManager = $backendUrlManager;
        $this->registry = $registry;
        $this->context = $context;
        $this->rendererFieldset = $rendererFieldset;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        /** @var \Mirasvit\Helpdesk\Model\Rule $rule */
        $rule = $this->registry->registry('current_rule');

        $renderer = $this->widgetFormRendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('*/rule/newConditionHtml/form/rule_conditions_fieldset', ['form_name' => $this->formName])
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Apply the rule only if the following conditions are met.')]
        )->setRenderer(
            $renderer
        );

        $rule->getConditions()->setFormName($this->formName);
        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'legend' => __(
                    'Apply the rule only if the following conditions are met.'
                ),
                'required' => true,
                'data-form-part' => $this->formName,
            ]
        )->setRule(
            $rule
        )->setRenderer(
            $this->conditions
        )->setFormName($this->formName);

        $form->setValues($rule->getData());
        $this->setConditionFormName($rule->getConditions(), $this->formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare content for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Apply the rule only if the following conditions are met.');
    }

    /**
     * Prepare title for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Apply the rule only if the following conditions are met.');
    }

    /**
     * Returns status flag about this tab can be showen or not.
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not.
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
