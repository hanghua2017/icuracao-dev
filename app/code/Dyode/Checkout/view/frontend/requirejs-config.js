/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout shipping core js file
 *
 * @module    Dyode_Checkout
 * @author    Mathew Joseph <mathew.joseph@dyode.com>
 * @copyright Copyright © Dyode
 */
var config = {
    config: {
        mixins: {
            'Magento_Tax/js/view/checkout/summary/shipping': {
                'Dyode_Checkout/js/mixin/summary-shipping-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Dyode_Checkout/js/mixin/shipping-mixin': true
            },
            'Magento_Checkout/js/view/summary/totals': {
                'Dyode_Checkout/js/mixin/summary/totals-mixin': true
            },
            'Magento_SalesRule/js/view/summary/discount': {
                'Dyode_Checkout/js/mixin/summary/totals-mixin': true
            },
            'Magento_Checkout/js/view/progress-bar': {
                'Dyode_Checkout/js/mixin/progress-bar-mixin': true
            }
        }
    }
};
