//Sign up onclick input show hide
require([
  'jquery'
], function($){
  $('input[name="curacaocustid"]').attr("placeholder", "Do you have a Curacao Account?");
  $('.field-curacaocustid').hide();
  $('input[name="account-check"]').on('click', function () {
      if ($(this).prop('checked')) {
          $('.field-curacaocustid').fadeIn();
      } else {
          $('.field-curacaocustid').hide();
      }
  });
});
