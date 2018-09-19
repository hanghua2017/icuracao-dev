<?php
/**
 * Dyode_Pricebeat extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Dyode
 *                     @package   Dyode_Pricebeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Pricebeat\Controller\Adminhtml\Form;

class View extends \Dyode\Pricebeat\Controller\Adminhtml\Form
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    protected $_coreRegistry = null;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Dyode\Pricebeat\Model\FormFactory $formFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Dyode\Pricebeat\Model\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context

    )
    {
        $this->backendSession    = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_coreRegistry = $registry;

        parent::__construct($formFactory, $registry, $resultRedirectFactory, $context);
    }
    /**
* Core registry
*
* @var \Magento\Framework\Registry
*/
// protected $_coreRegistry = null;

// public function __construct(
// \Magento\Backend\Model\Session $backendSession,
// \Magento\Framework\View\Result\PageFactory $resultPageFactory,
// \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
// \Dyode\Pricebeat\Model\FormFactory $formFactory,
// \Magento\Framework\Registry $registry,
// \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
// \Magento\Backend\App\Action\Context $context
//
// )
// {
// $this->backendSession = $backendSession;
// $this->resultPageFactory = $resultPageFactory;
// $this->resultJsonFactory = $resultJsonFactory;
// $this->_coreRegistry = $registry;
// parent::__construct($formFactory, $registry, $resultRedirectFactory, $context);
// }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dyode_Pricebeat::form');
    }





    public function execute() {
        $id = $this->getRequest()->getParam('form_id');
        $model = $this->_objectManager->create('Dyode\Pricebeat\Model\Form');
        
        if($id){
        $model->load($id);
        if(!$model->getId()){
        $this->messageManager->addError(__('This Form no longer exits. '));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('dyode_pricebeat*/*/view');
        }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
        $model->setData($data);
        }

        $this->_coreRegistry->register('dyode_dyode_form_data', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
        $id ? __('Edit Product FAQ') : __('New Product FAQ'),
        $id ? __('Edit Product FAQ') : __('New Product FAQ')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Product FAQs'));
        $resultPage->getConfig()->getTitle()
        ->prepend($model->getId() ? $model->getname() : __('New Product FAQs'));

        return $resultPage;
    }


}
