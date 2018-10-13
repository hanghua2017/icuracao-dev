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
          if ($("body").hasClass('catalogsearch-result-index') || $("body").hasClass('catalog-category-view')){
          $(this).scrollTop(0);
          }
        });

        // start price format js - adding superscript
        $('.price-box .price').each(function () {
            var $this = $(this),
                $val = $this.text(),
                dec_pos = $val.indexOf('.');
            $this.html($val.substring(0, dec_pos) + '.<sup>' + $val.substring(dec_pos + 1) + '</sup>');
        });

        $('.original-price').each(function () {
            var $this = $(this),
                $val = $this.text(),
                dec_pos = $val.indexOf('.');
                if(dec_pos){
                  $this.html($val.substring(0, dec_pos) + '.<sup>' + $val.substring(dec_pos + 1) + '</sup>');
                }
        });

        $('.discount-price').each(function () {
            var $this = $(this),
                $val = $this.text(),
                dec_pos = $val.indexOf('.');
                if(dec_pos){
                  $this.html($val.substring(0, dec_pos) + '.<sup>' + $val.substring(dec_pos + 1) + '</sup>');
                }
        });
        // end price format js - adding superscript
    });
