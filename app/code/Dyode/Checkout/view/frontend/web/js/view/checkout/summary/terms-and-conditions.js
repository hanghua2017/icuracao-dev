/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module.
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define(['uiComponent'], function (Component) {
    return Component.extend({
        termsAndConditionLink: window.checkoutConfig.terms_and_condition,
        privacyLink: window.checkoutConfig.privacy_link,

        getTcLink: function () {
            if (this.termsAndConditionLink) {
                return this.termsAndConditionLink;
            }

            return '#';
        },

        getPrivacyLink: function () {
            if (this.termsAndConditionLink) {
                return this.termsAndConditionLink;
            }

            return '#';
        },

        fireTcLinkAction: function (model) {
            if (!model.termsAndConditionLink) {
                return false;
            }

            return true;
        },

        firePrivacyLinkAction: function (model) {
            if (!model.privacyLink) {
                return false;
            }

            return true;
        }
    });
});
