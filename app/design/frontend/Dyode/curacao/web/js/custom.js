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
        /*js for fixed header ends here*/

        /*js for making the link active on Sale Page starts here*/
        var category_link = document.querySelector('a[href="'+document.URL+'"]');
        $('a[href="'+document.URL+'"]').addClass('active');
        /*js for making the link active on Sale Page ends here*/
      });
      //on click of pagination load plp page from top
        $( document ).ajaxStop(function() {
          if ($("body").hasClass('catalogsearch-result-index')){
          $(this).scrollTop(0);
          }
        });
    });
