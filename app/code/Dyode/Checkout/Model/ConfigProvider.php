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
   protected $_customerSession;
   protected $_helper;
   protected $_customerRepositoryInterface;
   protected $_curacaoId;
   protected $_cart;
   protected $_priceHelper;
   protected $_canApply;
   protected $_linked;

   public function __construct(\Magento\Framework\Pricing\Helper\Data $priceHelper,\Magento\Customer\Model\Session $customerSession,\Magento\Checkout\Model\Cart $cart, \Dyode\ARWebservice\Helper\Data $helper,\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface, LayoutInterface $layout, $blockId)
   {
       $this->_customerSession = $customerSession;
       $this->_layout = $layout;
       $this->_helper = $helper;
       $this->_customerRepositoryInterface = $customerRepositoryInterface;
       $this->_cart = $cart;
       $this->_priceHelper = $priceHelper;
   }

   public function getConfig()
   {
      $configArr['canapply']    =   $this->_canApply;
      $configArr['limit']       =   $this->getLimit();
      $configArr['total']       =   $this->getDownPayment();
      $configArr['linked']      =   $this->_linked;
      return $configArr;
   }

    public function getLimit(){
        $curaAccId =  $this->getCuracaoId();
            if($curaAccId){
                $this->_curacaoId = $curaAccId;
                $result = $this->_helper->getCreditLimit($curaAccId);
                $limit = (float)$result->CREDITLIMIT;
                $this->_canApply = 1;
                $formattedCurrencyValue = $this->_priceHelper->currency($limit, true, false);
                return $formattedCurrencyValue;
            }
            return false;
   }

   public function getDownPayment(){
        $curaAccId =  $this->getCuracaoId();
        if($curaAccId){
            $subTotal = $this->_cart->getQuote()->getSubtotal();
            $params = array('cust_id'=>$curaAccId,'amount'=>$subTotal);
            $result = $this->_helper->verifyPersonalInfm($params);
            if($result)
                $this->_customerSession->setDownPayment($result);
            else
                $result = 0;
            $formattedCurrencyValue = $this->_priceHelper->currency($result, true, false);
            return $formattedCurrencyValue;
        }
        return false;    	   
    }
   public function getCuracaoId(){
        if (!$this->_customerSession->getCustomerId()) {
            return false;
        }
        $customerId = $this->_customerSession->getCustomerId();
        if($customerId){
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $curaAccId = $customer->getCuracaocustid();
            if($curaAccId){
                $this->_linked = true;
                return $curaAccId;
            } else {
                $this->_linked = false;
                return false;
            }
            
        }
        return false;
   }

}
?>
