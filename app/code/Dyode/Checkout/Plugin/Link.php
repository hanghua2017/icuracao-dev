<?php
 
namespace Dyode\Checkout\Plugin;
 
class Link
{    
  
    public function afterGetCheckoutUrl(\Magento\Checkout\Block\Onepage\Link $subject, $result)
    {
        return str_replace("/default/","/",$subject->getUrl('checkout'));
    }
    
  
}
?>