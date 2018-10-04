<?php
namespace Dyode\Threshold\Model;

use \Magento\Framework\Model\AbstractModel;

class Threshold extends \Magento\Framework\View\Element\Template {

   public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,  
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	\Magento\Framework\File\Csv $csv,
	array $data = []
	) {
	    $this->scopeConfig = $scopeConfig;
	    $this->csv = $csv;
	    parent::__construct($context, $data);
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