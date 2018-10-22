// override price format to swap decimal and dot in spanish
define([
    'jquery',
    'underscore'
   ],
function ($, _) {
    'use strict';
    return function (target) {
        target.formatPrice = function formatPrice(amount, format, isShowSign) {
            var s = '',
            precision, integerRequired, decimalSymbol, groupSymbol, groupLength, pattern, i, pad, j, re, r, am;

            format = _.extend(globalPriceFormat, format);

            // copied from price-option.js | Could be refactored with varien/js.js

            precision = isNaN(format.requiredPrecision = Math.abs(format.requiredPrecision)) ? 2 : format.requiredPrecision;
            integerRequired = isNaN(format.integerRequired = Math.abs(format.integerRequired)) ? 1 : format.integerRequired;
            decimalSymbol = format.decimalSymbol === undefined ? '.' : format.decimalSymbol;
            groupSymbol = format.groupSymbol === undefined ? ',' : format.groupSymbol;
            groupLength = format.groupLength === undefined ? 3 : format.groupLength;
            pattern = format.pattern || '%s';

            if (isShowSign === undefined || isShowSign === true) {
                s = amount < 0 ? '-' : isShowSign ? '+' : '';
            } else if (isShowSign === false) {
                s = '';
            }
            pattern = pattern.indexOf('{sign}') < 0 ? s + pattern : pattern.replace('{sign}', s);

            // we're avoiding the usage of to fixed, and using round instead with the e representation to address
            // numbers like 1.005 = 1.01. Using ToFixed to only provide trailig zeroes in case we have a whole number
            i = parseInt(
                    amount = Number(Math.round(Math.abs(+amount || 0) + 'e+' + precision) + ('e-' + precision)),
                    10
                ) + '';
            pad = i.length < integerRequired ? integerRequired - i.length : 0;

            i = stringPad('0', pad) + i;

            j = i.length > groupLength ? i.length % groupLength : 0;
            re = new RegExp('(\\d{' + groupLength + '})(?=\\d)', 'g');

            // replace(/-/, 0) is only for fixing Safari bug which appears
            // when Math.abs(0).toFixed() executed on '0' number.
            // Result is '0.-0' :(

            am = Number(Math.round(Math.abs(amount - i) + 'e+' + precision) + ('e-' + precision));
            r = (j ? i.substr(0, j) + groupSymbol : '') +
                i.substr(j).replace(re, '$1' + groupSymbol) +
                (precision ? decimalSymbol + am.toFixed(2).replace(/-/, 0).slice(2) : '');

            return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        };

        return target;
    };
});