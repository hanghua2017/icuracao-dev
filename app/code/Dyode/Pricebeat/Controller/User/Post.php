<?php
namespace Dyode\Pricebeat\Controller\User;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

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
       echo "hello from the controller";
       exit();
      
    }
}
