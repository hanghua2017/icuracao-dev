define([
    "jquery",
    "jquery/ui"
], function($) {


    //creating jquery widget
    $.widget('Test.grid', {
        _create: function() {
            console.log('hey, js is loaded!')
            //bind click event of elem id
            this.element.on('click', function(e){
                console.log('Click ME!')
            });
        }

    });

    return $.Test.grid;
});
