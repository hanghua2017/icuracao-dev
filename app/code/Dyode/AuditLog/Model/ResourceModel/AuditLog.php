<?php

namespace Dyode\AuditLog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Dyode\AuditLog\Model\AuditLogFactory;

class AuditLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var connection
     */
    protected $connection;

    /**
     * @var mainTable
     */
    protected $getMainTable;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        AuditLogFactory $auditLogFactory,
        $resourcePrefix = null
    )
    {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->getMainTable = "dyode_audit_log";
        $this->connection = $this->getConnection();
        $this->auditLogFactory = $auditLogFactory;
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init("dyode_audit_log", $this->_idFieldName);
    }

    /**
     * Get Log Details by Id
     *
     * @param $logId
     * @return array
     */
    public function getAuditLogDetailsById($logId)
    {
        $select = $this->connection->select()->from("dyode_audit_log")->where('id = ' . $logId);
        $details = $this->connection->fetchAll($select);

        return $details;
    }

    /**
     * Saves Audit Log
     *
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
