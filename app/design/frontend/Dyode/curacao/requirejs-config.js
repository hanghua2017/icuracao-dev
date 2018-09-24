var config = {
    map: {
        '*': {
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
