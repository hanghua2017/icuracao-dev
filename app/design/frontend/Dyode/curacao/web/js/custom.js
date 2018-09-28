require([
			'jquery',
	], function($) {
    /*js for fixed header starts here*/
    $(document).ready(
        function() {
         var  header = $(".page-header");
				 var page_main = $(".page-main");
         var ns = "nav-scrolled";
				 var ms = "main-scrolled";
         var min_width = window.matchMedia("(min-width: 1025px)")
				 if(min_width.matches){
					 $(window).scroll(function() {
	          if( $(this).scrollTop() > 158 ) {
	            header.addClass(ns);
							page_main.addClass(ms);
	          } else {
	            header.removeClass(ns);
							page_main.removeClass(ms);
	           }
	         });
				 }
    });
  	/*js for fixed header ends here*/
});
