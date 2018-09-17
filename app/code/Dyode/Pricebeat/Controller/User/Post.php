<?php
namespace Dyode\Pricebeat\Controller\User;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Post extends Action
{
    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->validatedParams();
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
        if (trim($request->getParam('attachment')) !== '') {
            throw new LocalizedException(__('image is missing'));
        }

        //Add your more validations here
        return $request->getParams();
    }
}
