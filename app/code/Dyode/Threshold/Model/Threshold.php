<?php
namespace Dyode\Threshold\Model;

use \Magento\Framework\Model\AbstractModel;

class Threshold extends \Magento\Framework\Model\AbstractModel {

   public function __construct( 
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\File\Csv $csv,
	) {
	    $this->scopeConfig = $scopeConfig;
	    $this->csv = $csv;
	}


	public function getThreshold(){

		$value = $this->scopeConfig->getValue('dyode_threshold_section/threshold_group/threshold_file_upload', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		$file = 'pub/media/uploads/'.$value;

      	$data = $this->csv->getData($file);
        //first line are the headers
        if($data)
        {	
	        $headers = $data[0];

	        $allThresholdData = array();
	        for($i=1; $i<count($data); $i++){
	           $threshold = array();
	        foreach ($headers as $index=>$attributeCode) {
	           $threshold[$attributeCode] = $data[$i][$index];
	        }
	           $allThresholdData[] = $threshold;
			        
			}
		   return $allThresholdData;
		}
		return false;
    }
}
