<?php
namespace Magento\CustomerCustomAttributes\Helper\Customer;

/**
 * Proxy class for @see \Magento\CustomerCustomAttributes\Helper\Customer
 */
class Proxy extends \Magento\CustomerCustomAttributes\Helper\Customer implements \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\CustomerCustomAttributes\Helper\Customer
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\CustomerCustomAttributes\\Helper\\Customer', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared', '_instanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\CustomerCustomAttributes\Helper\Customer
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeFormOptions()
    {
        return $this->_getSubject()->getAttributeFormOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function filterPostData($data)
    {
        return $this->_getSubject()->filterPostData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidateRules($frontendInput, $validateRules)
    {
        return $this->_getSubject()->checkValidateRules($frontendInput, $validateRules);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeInputTypes($inputType = null)
    {
        return $this->_getSubject()->getAttributeInputTypes($inputType);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendInputOptions()
    {
        return $this->_getSubject()->getFrontendInputOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValidateFilters()
    {
        return $this->_getSubject()->getAttributeValidateFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeFilterTypes()
    {
        return $this->_getSubject()->getAttributeFilterTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeElementScopes()
    {
        return $this->_getSubject()->getAttributeElementScopes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeDefaultValueByInput($inputType)
    {
        return $this->_getSubject()->getAttributeDefaultValueByInput($inputType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeValidateRules($inputType, array $data)
    {
        return $this->_getSubject()->getAttributeValidateRules($inputType, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        return $this->_getSubject()->getAttributeBackendModelByInputType($inputType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        return $this->_getSubject()->getAttributeSourceModelByInputType($inputType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeBackendTypeByInputType($inputType)
    {
        return $this->_getSubject()->getAttributeBackendTypeByInputType($inputType);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserDefinedAttributeCodes()
    {
        return $this->_getSubject()->getUserDefinedAttributeCodes();
    }

    /**
     * {@inheritdoc}
     */
    public function getDateFormat()
    {
        return $this->_getSubject()->getDateFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        return $this->_getSubject()->isModuleOutputEnabled($moduleName);
    }
}
