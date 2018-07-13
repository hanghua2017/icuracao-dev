<?php
/*
Date: 03/07/2018
Author :Kavitha
*/

namespace Dyode\Linkaccount\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /*==== Verify Customer Account===*/
    public function verifyCustomerAccount(){
        
    }
    

}
?>