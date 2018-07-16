define([
        'jquery',
        'mage/translate'
    ],
    function($,phoneverify){

        return {
            verifyData:function(ajaxurl){
                $(document).on('click','#sendtext',function (event){
                    event.preventDefault();
                    alert("clicked");
                  /*  $.ajax({
                        url:ajaxurl,
                        type:'POST',
                        showLoader: true,
                        dataType:'json',
                        data: {id:productId},
                        success:function(response){
                        }
                      });
                      */
                    });
                  }
                }
              });
