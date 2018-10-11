<?php

namespace Dyode\AuditLog\Model;

class AuditLog extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Dyode\AuditLog\Model\ResourceModel\AuditLog');
    }
}
