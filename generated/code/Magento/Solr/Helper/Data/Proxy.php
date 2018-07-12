<?php
namespace Magento\Solr\Helper\Data;

/**
 * Proxy class for @see \Magento\Solr\Helper\Data
 */
class Proxy extends \Magento\Solr\Helper\Data implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Solr\Helper\Data
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Solr\\Helper\\Data', $shared = true)
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
     * @return \Magento\Solr\Helper\Data
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
    public function getSolrConfigData($field, $storeId = null)
    {
        return $this->_getSubject()->getSolrConfigData($field, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        return $this->_getSubject()->getSearchConfigData($field, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function isSolrEnabled()
    {
        return $this->_getSubject()->isSolrEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isActiveEngine()
    {
        return $this->_getSubject()->isActiveEngine();
    }

    /**
     * {@inheritdoc}
     */
    public function prepareClientOptions($options = array())
    {
        return $this->_getSubject()->prepareClientOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        return $this->_getSubject()->isModuleOutputEnabled($moduleName);
    }
}
