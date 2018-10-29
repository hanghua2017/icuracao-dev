<?php

namespace Dyode\Threshold\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\File\Csv;

class Threshold
{

    const FILE_UPLOAD_CONFIG_PATH = 'dyode_threshold_section/threshold_group/threshold_file_upload';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * Threshold constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(ScopeConfigInterface $scopeConfig, Csv $csv)
    {
        $this->scopeConfig = $scopeConfig;
        $this->csv = $csv;
    }

    /**
     * Get threshold data
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getThreshold()
    {
        $value = $this->scopeConfig->getValue(self::FILE_UPLOAD_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        $file = 'pub/media/uploads/' . $value;
        $data = $this->csv->getData($file);

        //first line are the headers
        if ($data) {
            $headers = $data[0];

            $allThresholdData = [];
            for ($i = 1; $i < count($data); $i++) {
                $threshold = [];
                foreach ($headers as $index => $attributeCode) {
                    $threshold[$attributeCode] = $data[$i][$index];
                }
                $allThresholdData[] = $threshold;

            }
            return $allThresholdData;
        }
        return false;
    }
}
