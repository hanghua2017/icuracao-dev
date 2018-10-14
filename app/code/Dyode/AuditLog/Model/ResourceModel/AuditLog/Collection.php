<?php

namespace Dyode\AuditLog\Model\ResourceModel\AuditLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Dyode\AuditLog\Model\AuditLog',
            'Dyode\AuditLog\Model\ResourceModel\AuditLog'
        );
    }
}
