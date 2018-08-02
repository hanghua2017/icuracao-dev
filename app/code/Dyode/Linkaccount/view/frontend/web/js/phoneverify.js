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
                            msg = '<p class="error">Failed to send the code..</p>';
                          }
                          else{
                            msg = '<p class="success">Successfully send the code</p>';
                          }
                          $(".verifymsg").html(msg);
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
                              msg = '<p class="error">Failed to call..</p>';
                            }
                            else{
                              msg = '<p class="success">Verified...</p>';
                            }
                            $(".verifymsg").html(msg);
                          }
                      });

                    });
              }
        }
});
