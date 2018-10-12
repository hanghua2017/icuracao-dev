//price box widget override
define([
    'jquery',
    'underscore'
   ],
    function ($, _) {
        'use strict';
        return function (target) {
            target.formatPrice = function formatPrice(amount, format, isShowSign) {
                var pattern = this._super();
            };
        return target;
    };
});
