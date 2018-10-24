/**
 * Dyode_Checkout Magento2 Module.
 *
 * Extending Magento_Checkout core module.
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define(['underscore','Magento_Checkout/js/model/step-navigator'], function (_, stepNavigator) {

    var mixin = {

        /**
         * For force reload always redirect user to delivery-step.
         *
         */
        initialize: function () {
            window.location.hash = '#deliverySelection';

            var deliveryStep = _.findWhere(stepNavigator.steps.sort(stepNavigator.sortItems), function (element) {
                return element.code === 'deliverySelection' || element.alias === 'deliverySelection';
            });

            if (deliveryStep) {
                deliveryStep.isVisible(true);
            }

            this._super();

            return this;
        }
    };

    /**
     * Mixin of Magento_Checkout/js/view/shipping
     */
    return function (target) {
        return target.extend(mixin);
    };
});
