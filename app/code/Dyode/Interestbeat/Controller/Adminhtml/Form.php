<?php
/**
 * Dyode_Interestbeat extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Dyode
 *                     @package   Dyode_Interestbeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Interestbeat\Controller\Adminhtml;

abstract class Form extends \Magento\Backend\App\Action
{
    /**
     * Form Factory
     *
     * @var \Dyode\Interestbeat\Model\FormFactory
     */
    protected $FormFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     *
     * @param \Dyode\Interestbeat\Model\FormFactory $formFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Dyode\Interestbeat\Model\FormFactory $formFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->formFactory          = $formFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Form
     *
     * @return \Dyode\Interestbeat\Model\Form
     */
    protected function initForm()
    {
        $formId  = (int) $this->getRequest()->getParam('form_id');
        /** @var \Dyode\Interestbeat\Model\Form  */
        $form    = $this->formFactory->create();
        if ($formId) {
            $form->load($formId);
        }
        $this->coreRegistry->register('dyode_interestbeat_form', $form);
        return $form;
    }
}
