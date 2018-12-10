<?php 
/**
 * Dyode_Store Magento2 Module.
 *
 * Extending Magento_Store
 *
 * @package   Dyode
 * @module    Dyode_Store
 * @author    kavitha@dyode.com
 */

namespace Dyode\Store\Model;

class Store extends \Magento\Store\Model\Store
{
      /**
     * @var StoreManagerInterface
     */
    private $_storeManager;
    

     /**
     * Add store code to url in case if it is enabled in configuration
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseStoreView($url)
    {
        if($this->getCode() == 'default'){
            return $url; 
        }
        if ($this->isUseStoreInUrl()) {
            $url .= $this->getCode() . '/';
        }
        return $url;
    }
     /**
     * Retrieve url using store configuration specific
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    // public function getUrl($route = '', $params = [])
    // {
    //     /** @var $url UrlInterface */
    //     $url = $this->_url->setScope($this);
    //     if ($this->_storeManager->getStore()->getId() != $this->getId()) {
    //         $params['_scope_to_url'] = true;
    //     }
    //     echo $url->getUrl($route, $params);
    //     return $url->getUrl($route, $params);
    // }
}
