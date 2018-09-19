<?php
namespace Dyode\Pricebeat\Controller\User;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Dyode\Pricebeat\Model\FormFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use \Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;
use \Dyode\Pricebeat\Model\Upload;
use \Magento\Framework\Filesystem;
use \Magento\MediaStorage\Model\File\UploaderFactory;

use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Customer\Model\Session;

class Post extends Action
{
    protected $_modelFormFactory;
    protected $resultPageFactory;
    protected $_sessionManager;
    protected  $fileUploaderFactory;
    protected  $filesystem;

    protected $customerSession;


    public function __construct(
      Context $context,
      FormFactory $modelFormFactory,
      PageFactory $pageFactory,
      SessionManagerInterface $sessionManager,
      Filesystem $fileSystem,
      Session $customerSession


      )
    {
        $this->resultPageFactory = $pageFactory;
        $this->_modelFormFactory = $modelFormFactory;
        $this->_sessionManager = $sessionManager;
        $this->fileSystem = $fileSystem;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->validatedParams();
        $this->getFormData();
        $this->_redirect('pricebeat');
        // echo "hello from the controller";
        // exit();
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
       $filedata           = $this->getRequest()->getFiles('upload_file');
       $date               = date('Y-m-d h:i:sa');
           if ($_FILES['upload_file']['name']) {
               try {
                   $uploader = $this->_objectManager->create(
                       'Magento\MediaStorage\Model\File\Uploader',
                       ['fileId' => 'upload_file']
                   );
                   $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                   $uploader->setAllowRenameFiles(true);
                   $uploader->setFilesDispersion(true);

                   $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                   $path = $mediaDirectory->getAbsolutePath('images/');
                   $imagePath = $uploader->save($path);
                   $data['upload_file'] = $imagePath['file'];
                   // var_dump($data);
                   // exit();
               } catch (Exception $e) {
                   $this->messageManager->addException($e->getMessage());
                   return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
               }
           }



       $FormModel->setData('first_name', $data['first_name']);
       $FormModel->setData('last_name', $data['last_name']);
       $FormModel->setData('email', $data['email']);
       $FormModel->setData('phonenumber', $data['phonenumber']);
       $FormModel->setData('account_number', $data['account_number']);
       $FormModel->setData('invoice_number', $data['invoice_number']);
       $FormModel->setData('product_url', $data['product_url']);
       $FormModel->setData('product_image_url', $data['product_image_url']);
       $FormModel->setData('product_image_url', $data['upload_file']);
       $FormModel->setData('created_date', $date);
       $FormModel->setData('status', $data['status']);

       $FormModel->save();

       $this->_redirect('blog/index');
       $this->messageManager->addSuccess(__('The data has been saved.'));
    }


}
