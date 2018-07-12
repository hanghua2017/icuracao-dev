<?php
namespace Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition;

/**
 * Factory class for @see \Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter
 */
class FilterFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\AdvancedSalesRule\\Model\\ResourceModel\\Rule\\Condition\\Filter')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
