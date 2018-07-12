<?php
namespace Magento\Bundle\Pricing\Adjustment\Calculator;

/**
 * Proxy class for @see \Magento\Bundle\Pricing\Adjustment\Calculator
 */
class Proxy extends \Magento\Bundle\Pricing\Adjustment\Calculator implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Bundle\Pricing\Adjustment\Calculator
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Bundle\\Pricing\\Adjustment\\Calculator', $shared = true)
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
     * @return \Magento\Bundle\Pricing\Adjustment\Calculator
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
    public function getAmount($amount, \Magento\Framework\Pricing\SaleableInterface $saleableItem, $exclude = null, $context = array())
    {
        return $this->_getSubject()->getAmount($amount, $saleableItem, $exclude, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinRegularAmount($amount, \Magento\Catalog\Model\Product $saleableItem, $exclude = null)
    {
        return $this->_getSubject()->getMinRegularAmount($amount, $saleableItem, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxAmount($amount, \Magento\Catalog\Model\Product $saleableItem, $exclude = null)
    {
        return $this->_getSubject()->getMaxAmount($amount, $saleableItem, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRegularAmount($amount, \Magento\Catalog\Model\Product $saleableItem, $exclude = null)
    {
        return $this->_getSubject()->getMaxRegularAmount($amount, $saleableItem, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsAmount(\Magento\Catalog\Model\Product $saleableItem, $exclude = null, $searchMin = true, $baseAmount = 0, $useRegularPrice = false)
    {
        return $this->_getSubject()->getOptionsAmount($saleableItem, $exclude, $searchMin, $baseAmount, $useRegularPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountWithoutOption($amount, \Magento\Catalog\Model\Product $saleableItem)
    {
        return $this->_getSubject()->getAmountWithoutOption($amount, $saleableItem);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateBundleAmount($basePriceValue, $bundleProduct, $selectionPriceList, $exclude = null)
    {
        return $this->_getSubject()->calculateBundleAmount($basePriceValue, $bundleProduct, $selectionPriceList, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function createSelectionPriceList($option, $bundleProduct, $useRegularPrice = false)
    {
        return $this->_getSubject()->createSelectionPriceList($option, $bundleProduct, $useRegularPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions($option, $selectionPriceList, $searchMin = true)
    {
        return $this->_getSubject()->processOptions($option, $selectionPriceList, $searchMin);
    }
}
