<?php

namespace Dyode\AuditLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Dyode\AuditLog\Model\ResourceModel\AuditLog as AuditLogResourceModel;

class AuditLog extends AbstractHelper
{

    /**
     * @var \Dyode\AuditLog\Model\ResourceModel\AuditLog
     */
    var $auditLog;

    /**
     * AuditLog constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLogFactory
     *
     */
    public function __construct(
        Context $context,
        AuditLogResourceModel $auditLog
    ) {
        $this->auditLog = $auditLog;
        parent::__construct($context);
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function saveAuditLog($data)
    {
        try {

           $this->auditLog->saveAuditLog($data);

           return true;
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }
}
