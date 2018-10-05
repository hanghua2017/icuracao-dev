/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout shipping core js file
 *
 * @package   Dyode
 * @module    Dyode_Checkout
 * @author    Mathew Joseph <mathew.joseph@dyode.com>
 * @copyright Copyright © Dyode
 */
var config = {
    map: {
        '*': {
            "Magento_Checkout/js/view/shipping" : 
            "Dyode_Checkout/js/view/shipping"
        }
    }
};