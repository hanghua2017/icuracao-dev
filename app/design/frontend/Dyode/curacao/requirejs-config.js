var config = {
    "paths": {
      'accordion':'js/cms-accordion',
      'checkbox':'js/onclick',
      'customjs':'js/custom',
      'custom-select':'js/custom-select'
    },
    "shim":{
      'accordion': {
          'deps': ['jquery']
      },
      'checkbox': {
          'deps': ['jquery']
      },
      'custom-select': {
          'deps': ['jquery']
      },
      'customjs': {
          'deps': ['jquery']
      }
    }
};

//override priceutils.js
var config = {
    'config': {
        'mixins': {
            'Magento_Catalog/js/price-utils': {
                'js/price-utils-mixin': true
            }
        }
    }
};
