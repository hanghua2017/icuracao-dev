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



namespace Mirasvit\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Mirasvit\Rma\Api;
use Mirasvit\Rma\Api\Data\DataInterface;

/**
 * @method \Mirasvit\Rma\Model\ResourceModel\Field\Collection|\Mirasvit\Rma\Model\Field[] getCollection()
 * @method \Mirasvit\Rma\Model\Field load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rma\Model\Field setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rma\Model\Field setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rma\Model\ResourceModel\Field getResource()
 * @method Api\Data\FieldSearchResultsInterface getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
 */
class Field extends \Magento\Framework\Model\AbstractModel
    implements \Mirasvit\Rma\Api\Data\FieldInterface, IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rma\Model\ResourceModel\Field');
    }

    public function __construct(
        \Mirasvit\Rma\Model\FieldFactory $fieldFactory,
        \Mirasvit\Rma\Helper\Storeview $rmaStoreview,
        \Mirasvit\Rma\Helper\Locale $localeData,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->resource = $resource;
        $this->rmaStoreview = $rmaStoreview;
        $this->localeData = $localeData;
        $this->context = $context;
        $this->registry = $registry;
        $this->resourceCollection = $resourceCollection;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->localeData->getLocaleValue($this, self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->localeData->setLocaleValue($this, self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::KEY_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::KEY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        if ($this->getType() == 'select') {
            $data = $this->getData(self::KEY_VALUES);
            $rows = explode("\n", $data);
            $values = [];
            foreach ($rows as $row) {
                if (trim($row)) {
                    $keyValue = explode(' | ', $row);
                    $values[$keyValue[0]] = $keyValue[1];
                }
            }
            return $values;
        } else {
            return $this->getData(self::KEY_VALUES);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setValues($values)
    {
        return $this->setData(self::KEY_VALUES, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getData(self::KEY_IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::KEY_IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::KEY_SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRequiredStaff()
    {
        return $this->getData(self::KEY_IS_REQUIRED_STAFF);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequiredStaff($isRequiredStaff)
    {
        return $this->setData(self::KEY_IS_REQUIRED_STAFF, $isRequiredStaff);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRequiredCustomer()
    {
        return $this->getData(self::KEY_IS_REQUIRED_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequiredCustomer($isRequiredCustomer)
    {
        return $this->setData(self::KEY_IS_REQUIRED_CUSTOMER, $isRequiredCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleCustomer()
    {
        return $this->getData(self::KEY_IS_VISIBLE_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleCustomer($isVisibleCustomer)
    {
        return $this->setData(self::KEY_IS_VISIBLE_CUSTOMER, $isVisibleCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsEditableCustomer()
    {
        return $this->getData(self::KEY_IS_EDITABLE_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEditableCustomer($isEditableCustomer)
    {
        return $this->setData(self::KEY_IS_EDITABLE_CUSTOMER, $isEditableCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleCustomerStatus()
    {
        return $this->getData(self::KEY_VISIBLE_CUSTOMER_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibleCustomerStatus($visibleCustomerStatus)
    {
        return $this->setData(self::KEY_VISIBLE_CUSTOMER_STATUS, $visibleCustomerStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsShowInConfirmShipping()
    {
        return $this->getData(self::KEY_IS_SHOW_IN_CONFIRM_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsShowInConfirmShipping($isShowInConfirmShipping)
    {
        return $this->setData(self::KEY_IS_SHOW_IN_CONFIRM_SHIPPING, $isShowInConfirmShipping);
    }

    const CACHE_TAG = 'rma_field';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_field';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_field';

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function afterCommitCallback()
    {
        $this->getResource()->afterCommitCallback($this);

        return parent::afterCommitCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);

        return parent::afterDeleteCommit();
    }
}
