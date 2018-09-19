define([
  'jquery',
  'jquery/ui',
  'Magento_Search/js/form-mini' // usually widget can be found in /lib/web/mage dir
], function($){
 
  $.widget('dyode.dyodesearch', $.mage.quickSearch, { 
      _create: function () {
      	console.log('set');
      }

  });
 
  return $.dyode.dyodesearch;
});
