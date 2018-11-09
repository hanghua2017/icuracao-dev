define([
        'jquery',
        'mage/translate'
    ],
    function($,phoneverify){

        return {
            verifyData:function(ajaxurl){
                $(document).on('click','#sendtext',function (event){
                    event.preventDefault();
                    $.ajax({
                        url:ajaxurl,
                        type:'POST',
                        showLoader: true,
                        dataType:'json',
                        data: {verifytype:0},
                        success:function(response){
                          if(response == -1){
                            msg = '<p class="error">Unfortunately, we could not successfully send verification code to your phone.</p>';
                          }
                          else{
                            $(".codeverify").css('display','block');
                            msg = '<p class="success">Please enter the code that sent to your phone number:</p>';
                          }
                          $(".messages").html('');
                          $(".phoneverify").html(msg);
                        }
                    });

                  });
                  /*=== Function for voice text ===*/
                  $(document).on('click','#voice',function (event){
                      event.preventDefault();
                      $.ajax({
                          url:ajaxurl,
                          type:'POST',
                          showLoader: true,
                          dataType:'json',
                          data: {verifytype:1},
                          success:function(response){
                            if(response == -1){
                              msg = '<p class="error">Unfortunately, we could not place a call to your phone.</p>';             
                            }
                            else{
                              $(".codeverify").css('display','block');
                              msg = '<p class="success">Please enter the code for completing the verification : </p>';
                            }
                            $(".messages").html('');
                            $(".phoneverify").html(msg);
                          }
                      });

                    });
              }
        }
});
