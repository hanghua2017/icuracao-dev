/**
 * Dyode_CuracaoPayment Magento2 Module.
 *
 * Provide the facility: curacao custom payment method
 *
 * @module    Dyode_CuracaoPayment
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {

    return Component.extend({
        defaults: {
            template: 'Dyode_CuracaoPayment/payment/curacaofullpayment'
        },
        getMailingAddress: function () {
            return window.checkoutConfig.payment.checkmo.mailingAddress;
        },
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];}
    });
});
