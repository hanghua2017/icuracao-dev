/**
 * Copyright © Magento, Inc. All rights reserved.
 */
 define([
    'jquery'
], function (jQuery) {
    return function (originalWidget) {
        jQuery.widget(
            'mage.quickSearch',
            jQuery['mage']['quickSearch'],
            {
                //overeriding core _create function
                _create: function () {
                    //call parent open for original functionality
                    this._super();
                    //enable search submit button
                    this.submitBtn.disabled = false;
                }
            }
        );
        return jQuery['mage']['quickSearch'];
    };
});