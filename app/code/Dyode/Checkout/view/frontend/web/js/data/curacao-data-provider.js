/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define(['ko'], function (ko) {
    var isUserLinked = !!window.checkoutConfig.curacaoPayment.linked;

    return {
        hasCuracaoCreditApplied: ko.observable(isUserLinked)
    };
});
