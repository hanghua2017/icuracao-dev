<?php

namespace Dyode\AuditLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Dyode\AuditLog\Model\AuditLogFactory;

class AuditLog extends AbstractHelper
{

    /**
     * @var \Dyode\AuditLog\Model\AuditLogFactory
     */
    var $auditLogFactory;

    /**
     * AuditLog constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Dyode\AuditLog\Model\AuditLogFactory $auditLogFactory
     *
     */
    public function __construct(
        Context $context,
        AuditLogFactory $auditLogFactory
    ) {
        $this->auditLogFactory = $auditLogFactory;
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
            $rowData = $this->auditLogFactory->create();
            $rowData->setData($data);
            $rowData->save();

            return true;
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }
}
