//Sign up onclick input show hide
require([
  'jquery'
], function($){
  $('input[name="curacaocustid"]').attr("placeholder", "Curacao Account Number");
  $('.field-curacaocustid').hide();
  $('input[name="account-check"]').on('click', function () {
      if ($(this).prop('checked')) {
          $('.field-curacaocustid').fadeIn();
      } else {
          $('.field-curacaocustid').hide();
      }
  });
});
