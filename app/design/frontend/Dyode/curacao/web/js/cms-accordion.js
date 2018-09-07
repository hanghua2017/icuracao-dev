require([
  'jquery'
], function($){
  $('.accordion-title').click(function(e) {
      // e.preventDefault();
      var $this = $(this);

      if ($(this).hasClass("active")) {
        $(this).removeClass("active");
      }
      else {
        $(".accordion-title").removeClass("active");
        $(this).addClass("active");
      }

      if ($this.next().hasClass('show')) {
          $this.next().removeClass('show');
          $this.next().slideUp(350);
      } else {
          $this.parent().parent().find('.accordion-panel').removeClass('show');
          $this.parent().parent().find('.accordion-panel').slideUp(350);
          $this.next().toggleClass('show');
          $this.next().slideToggle(350);
      }
  });
});
