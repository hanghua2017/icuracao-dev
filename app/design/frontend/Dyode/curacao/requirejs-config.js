var config = {
    map: {
        '*': {
            'accordion': 'curacao/js/cms-accordion',
            'checkbox': 'curacao/js/onclick'
        }
    },
    'deps': ["jquery"],
    "paths": {
      "customjs":'js/custom.js'
    },
    "shim":{
        "customjs": ["jquery"]
     }
};
