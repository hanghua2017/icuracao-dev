<?php
namespace Dyode\Pricebeat\Controller\User;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Dyode\Pricebeat\Model\FormFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Post extends Action
{
    protected $_modelFormFactory;
    protected $resultPageFactory;
    protected $_sessionManager;


    public function __construct(
      Context $context,
      FormFactory $modelFormFactory,
      PageFactory $pageFactory,
      SessionManagerInterface $sessionManager)
    {
        $this->resultPageFactory = $pageFactory;
        $this->_modelFormFactory = $modelFormFactory;
        $this->_sessionManager = $sessionManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->validatedParams();
        $this->getFormData();
        echo "hello from the controller";
        exit();
    }

    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('first_name')) === '') {
            throw new LocalizedException(__('First Name is missing'));
        }

        if (trim($request->getParam('last_name')) === '') {
            throw new LocalizedException(__('Last Name is missing'));
        }

        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (trim($request->getParam('phonenumber')) === '') {
            throw new LocalizedException(__('Phone Number is missing'));
        }
        if (trim($request->getParam('account_number')) === '') {
            throw new LocalizedException(__('Account Number is missing'));
        }
        if (trim($request->getParam('invoice_number')) === '') {
            throw new LocalizedException(__('Invoice Number is missing'));
        }
        if (trim($request->getParam('product_url')) === '') {
            throw new LocalizedException(__('product url Number is missing'));
        }
        if (trim($request->getParam('product_image_url')) !== '') {
            throw new LocalizedException(__('image is missing'));
        }

        //Add your more validations here
        return $request->getParams();
    }
    private function getFormData()
    {

       $resultRedirect     = $this->resultRedirectFactory->create();
       $FormModel          = $this->_modelFormFactory->create();
       $data               = $this->getRequest()->getPost();
       $date               = date('Y-m-d h:i:sa');

       $FormModel->setData('first_name', $data['first_name']);
       $FormModel->setData('last_name', $data['last_name']);
       $FormModel->setData('email', $data['email']);
       $FormModel->setData('phonenumber', $data['phonenumber']);
       $FormModel->setData('account_number', $data['account_number']);
       $FormModel->setData('invoice_number', $data['invoice_number']);
       $FormModel->setData('product_url', $data['product_url']);
       $FormModel->setData('product_image_url', $data['product_image_url']);


       $FormModel->setData('created_date', $date);
       $FormModel->setData('status', $data['status']);

       $FormModel->save();

       $this->_redirect('blog/index');
       $this->messageManager->addSuccess(__('The data has been saved.'));
    }
}
