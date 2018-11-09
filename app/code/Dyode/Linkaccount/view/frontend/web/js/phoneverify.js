define([
        'jquery',
        'mage/translate'
    ],
    function($,$t){

        return {
            verifyData:function(ajaxurl){
                $(".msg-outer").css('display','none');
                $(document).on('click','#sendtext',function (event){
                    event.preventDefault();
                    $.ajax({
                        url:ajaxurl,
                        type:'POST',
                        showLoader: true,
                        dataType:'json',
                        data: {verifytype:0},
                        success:function(response){
                          $(".msg-outer").css('display','block');
                          if(response == -1){
                            msg = '<p class="error">' + $t('Unfortunately, we could not successfully send verification code to your phone.')+ '</p>';
                          }
                          else{
                            $(".codeverify").css('display','block');
                            msg = '<p class="success">'+ $t('Please enter the code that sent to your phone number: ')+'</p>';
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
                            $(".msg-outer").css('display','block');
                            if(response == -1){
                              msg = '<p class="error">'+$t('Unfortunately, we could not place a call to your phone.')+'</p>';             
                            }
                            else{
                              $(".codeverify").css('display','block');
                              msg = '<p class="success">'+ $t('Please enter the code for the completion of verification : ')+'</p>';
                            }
                            $(".messages").html('');
                            $(".phoneverify").html(msg);
                          }
                      });

                    });
              }
        }
});
