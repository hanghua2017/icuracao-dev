<?php
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype\Edit;

/**
 * Interceptor class for @see \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype\Edit
 */
class Interceptor extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Eav\Model\Form\TypeFactory $formTypeFactory, \Magento\Eav\Model\Form\FieldsetFactory $fieldsetFactory, \Magento\Eav\Model\ResourceModel\Form\Fieldset\CollectionFactory $fieldsetsFactory, \Magento\Eav\Model\ResourceModel\Form\Element\CollectionFactory $elementsFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $formTypeFactory, $fieldsetFactory, $fieldsetsFactory, $elementsFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
