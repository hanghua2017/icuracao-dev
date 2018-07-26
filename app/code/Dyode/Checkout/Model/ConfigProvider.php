<?php
namespace Dyode\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
   /** @var LayoutInterface  */
   protected $_layout;
   protected $_cmsBlock;
   protected $_curacaoAccount;
   protected $_customerSession;
   protected $_customerRepository;
   protected $_curacaoAccountNum;


   public function __construct(
     \Magento\Customer\Model\Session $customerSession,
     \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
     LayoutInterface $layout, $blockId)
   {
       $this->_layout = $layout;
       $this->_customerSession = $customerSession;
       $this->_customerRepository = $customerRepository;
       $this->_cmsBlock = $this->constructBlock($blockId);
    //   $this->_curacaoAccountNum = $this->checkAccountLinked();
   }

   public function constructBlock($blockId){
       $block = $this->_layout->createBlock('Magento\Cms\Block\Block')
           ->setBlockId($blockId)->toHtml();
       return $block;
   }

   public function getConfig()
   {
       return [
           'cms_block' => $this->_cmsBlock
          // 'curacaonum'=> $this->_curacaoAccountNum
       ];
   }

   /*=== function to get the curacao account number===*/
   public function checkAccountLinked(){
       $curaAccId ='';
       if($this->_customerSession->isLoggedIn()){
         $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());
         $curaAccId = $customer->getCuracaocustid();
         if($curaAccId != ''){
            // Get the credit Limit

         }
       }
       return $curaAccId;
   }

}
?>
