define(['jquery'], function(jQuery){
    return function(originalWidget){
        
        jQuery.widget(
            'mage.quickSearch',              //named widget we're redefining            

            //jQuery.mage.dropdownDialog
            jQuery['mage']['quickSearch'],   //widget definition to use as
                                                //a "parent" definition -- in 
                                                //this case the original widget
                                                //definition, accessed using 
                                                //bracket syntax instead of 
                                                //dot syntax        

            {                                   //the new methods
                _create: function(){                    
                    //our new code here
                    console.log("I opened a dropdown!");

                    //call parent open for original functionality
                    this._super();  

                    this.submitBtn.disabled = false;            

                }
            });                                

        //return the redefined widget for `data-mage-init`
        //jQuery.mage.dropdownDialog
	        return jQuery['mage']['quickSearch'];
	    };
});