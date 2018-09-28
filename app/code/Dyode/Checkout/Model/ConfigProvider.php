<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       02/08/2018
 */
namespace Dyode\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
   /** @var LayoutInterface  */
   protected $_layout;
   protected $_cmsBlock;
   protected $_customerSession;
   protected $_helper;
   protected $_customerRepositoryInterface;
   protected $_curacaoId;
   protected $_cart;
   protected $_priceHelper;
   protected $_canApply;

   public function __construct(\Magento\Framework\Pricing\Helper\Data $priceHelper,\Magento\Customer\Model\Session $customerSession,\Magento\Checkout\Model\Cart $cart, \Dyode\ARWebservice\Helper\Data $helper,\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface, LayoutInterface $layout, $blockId)
   {
       $this->_customerSession = $customerSession;
       $this->_layout = $layout;
       $this->_helper = $helper;
       $this->_customerRepositoryInterface = $customerRepositoryInterface;
       $this->_cmsBlock = $this->constructBlock($blockId);
       $this->_cart = $cart;
       $this->_priceHelper = $priceHelper;
   }

   public function constructBlock($blockId){
       $block = $this->_layout->createBlock('Magento\Cms\Block\Block')
           ->setBlockId($blockId)->toHtml();
       return $block;
   }

   public function getConfig()
   {
      $configArr ['cms_block'] = $this->_cmsBlock;
      $configArr['canapply'] = $this->_canApply;
      $configArr['limit'] = $this->getLimit();
      $configArr['total']= $this->getDownPayment();
      return $configArr;
   }

    public function getLimit(){
       if (!$this->_customerSession->getCustomerId()) {
           return false;
       }
      $customerId = $this->_customerSession->getCustomerId();
      //$customer = $this->_customerRepositoryInterface->getById($customerId);
      //$curaAccId = $customer->getCuracaocustid();
      $curaAccId = '54420730';
      $this->_curacaoId = $curaAccId;
    //  $result = $this->_helper->getCreditLimit($curaAccId);
    //  $limit = (float)$result->CREDITLIMIT;
      $limit = 500.00;
      $this->_canApply = 1;
      $formattedCurrencyValue = $this->_priceHelper->currency($limit, true, false);
      return $formattedCurrencyValue;
   }

   public function getDownPayment(){
    //  $curaAccId = $this->_curacaoId;
      $curaAccId = '54420740';
    	$subTotal = $this->_cart->getQuote()->getSubtotal();
      $params = array('cust_id'=>$curaAccId,'amount'=>$subTotal);
    //  $result = $this->_helper->verifyPersonalInfm($params);
      $result = 10.00;
      $this->_customerSession->setDownPayment($result);
      $formattedCurrencyValue = $this->_priceHelper->currency($result, true, false);
      return $formattedCurrencyValue;
   }
}
?>
