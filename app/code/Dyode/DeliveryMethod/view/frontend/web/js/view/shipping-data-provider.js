/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/09/2018
 */

 /**
  * This holds shipping method information over each quote items
  * in a quote.
  */
define(['ko'], function(ko){
    return {
        shippingInfo: ko.observableArray([])
    }
});