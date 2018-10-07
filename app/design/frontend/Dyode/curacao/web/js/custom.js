require(['jquery'], function ($) {
    'use strict';
/*js for fixed header starts here*/
    $(document).ready(function () {
        var header = $('.page-header'),
            pageMain = $('.page-main'),
            ns = 'nav-scrolled',
            ms = 'main-scrolled';

        $(window).scroll(function () {
            if ($(this).scrollTop() > 158) {
                header.addClass(ns);
                pageMain.addClass(ms);
            } else {
                header.removeClass(ns);
                pageMain.removeClass(ms);
            }
        });
    });
		/*js for fixed header ends here*/
});
