<?php
namespace Magento\SalesRule\Model\Rule;

/**
 * Interceptor class for @see \Magento\SalesRule\Model\Rule
 */
class Interceptor extends \Magento\SalesRule\Model\Rule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\SalesRule\Model\CouponFactory $couponFactory, \Magento\SalesRule\Model\Coupon\CodegeneratorFactory $codegenFactory, \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory, \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF, \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array(), \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory = null, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory = null, \Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $localeDate, $couponFactory, $codegenFactory, $condCombineFactory, $condProdCombineF, $couponCollection, $storeManager, $resource, $resourceCollection, $data, $extensionFactory, $customAttributeFactory, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFromDate');
        if (!$pluginInfo) {
            return parent::getFromDate();
        } else {
            return $this->___callPlugins('getFromDate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getToDate');
        if (!$pluginInfo) {
            return parent::getToDate();
        } else {
            return $this->___callPlugins('getToDate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsFieldSetId($formName = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConditionsFieldSetId');
        if (!$pluginInfo) {
            return parent::getConditionsFieldSetId($formName);
        } else {
            return $this->___callPlugins('getConditionsFieldSetId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsFieldSetId($formName = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionsFieldSetId');
        if (!$pluginInfo) {
            return parent::getActionsFieldSetId($formName);
        } else {
            return $this->___callPlugins('getActionsFieldSetId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'beforeSave');
        if (!$pluginInfo) {
            return parent::beforeSave();
        } else {
            return $this->___callPlugins('beforeSave', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
