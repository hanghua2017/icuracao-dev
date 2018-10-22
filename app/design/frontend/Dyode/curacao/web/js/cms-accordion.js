require(['jquery'], function ($) {

    //make sure DOM is loaded
    $(function () {
        $('.accordion-title').click(function (e) {
            e.preventDefault();

            var $this = $(this);

            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            } else {
                $('.accordion-title').removeClass('active');
                $(this).addClass('active');
            }

            if ($this.next().hasClass('show-accordion')) {
                $this.next().removeClass('show-accordion');
                $this.next().slideUp(350);
            } else {
                $this.parent().parent().find('.accordion-panel').removeClass('show-accordion');
                $this.parent().parent().find('.accordion-panel').slideUp(350);
                $this.next().toggleClass('show-accordion');
                $this.next().slideToggle(350);
            }
        });
    });
    /*js for smooth scroll down the page starts here*/
      $(document).ready(function() {
  		  $('.beat-container a[href*="#"],.catalog-product-view .attribute a[href*="#"]').bind('click', function(e) {
  				e.preventDefault(); // prevent hard jump, the default behavior

  				var target = $(this).attr("href"); // Set the target as variable
          var mob_width = window.matchMedia("(max-width: 767px)");// screen width of mobiles devices

  				// perform animated scrolling by getting top-position of target-element and set it as scroll target
  				$('html, body').stop().animate({
  						scrollTop: $(target).offset().top - 65
  				}, 600, function() {
  						location.hash = target; //attach the hash (#jumptarget) to the pageurl
  				});
          //perform animated scrolling in mobiles
          if(mob_width.matches){
            $('html, body').stop().animate({
    						scrollTop: $(target).offset().top - 90
    				}, 600, function() {
    						location.hash = target; //attach the hash (#jumptarget) to the pageurl
    				});
          }

  				return false;
  		});
  });
  /*js for smooth scroll down the page starts here*/
});
