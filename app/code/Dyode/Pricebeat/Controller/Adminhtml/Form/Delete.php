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

class Delete extends \Dyode\Pricebeat\Controller\Adminhtml\Form
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('form_id');
        if ($id) {
            $title = "";
            try {
                /** @var \Dyode\Pricebeat\Model\Form $form */
                $form = $this->formFactory->create();
                $form->load($id);
                $title = $form->getTitle();
                $form->delete();
                $this->messageManager->addSuccess(__('The Pricebeat has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_dyode_pricebeat_form_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                $resultRedirect->setPath('dyode_pricebeat/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_dyode_pricebeat_form_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('dyode_pricebeat/*/edit', ['form_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Form to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('dyode_pricebeat/*/');
        return $resultRedirect;
    }
}