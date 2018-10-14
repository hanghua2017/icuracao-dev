<?php

namespace Dyode\AuditLog\Block\Adminhtml\Index;

use Dyode\AuditLog\Model\ResourceModel\AuditLog;
use Magento\Backend\Block\Template\Context;

class View extends \Magento\Backend\Block\Template
{

    /**
     * @var _auditLog
     */
    protected $_auditLog;

    public function __construct(
        Context $context,
        AuditLog $auditLog

    ) {
        $this->_auditLog = $auditLog;
        parent::__construct($context);
    }

    /**
     * Method for getting the survey response based on the survey id
     *
     * @return mixed
     */
    public function getAuditLogDetails()
    {
        $logId = $this->getRequest()->getParam('id');

        $details = $this->_auditLog->getAuditLogDetailsById($logId);
        return $details;
    }
}
