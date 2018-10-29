<?php

namespace Dyode\AuditLog\Controller\Adminhtml\Index;

use Dyode\AuditLog\Model\AuditLog;

class View extends \Magento\Backend\App\Action
{

    /**
     * @var $_auditLog
     */
    protected $_auditLog;

    /**
     * View constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param AuditLog $auditLog
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        AuditLog $auditLog
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_auditLog = $auditLog;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|
     * \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $logId = (int) $this->getRequest()->getParam('id');
        if ($logId) {
            $rowData = $this->_auditLog->load($logId);
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('auditlog/index/index');

                return;
            }
        }

        $this->_coreRegistry->register('row_data', $rowData);
        $resultPage = $this->_resultPageFactory->create();
        $title =  __('Audit Log Details');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }

    /**
     * Method for checking authorization
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dyode_AuditLog::audit_view');
    }
}
