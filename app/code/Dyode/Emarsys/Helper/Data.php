<?php
namespace Dyode\Emarsys\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMARSYS_LABEL_CONFIG_PATH = 'emarsys_settings/emarsys_settings';
  /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
    protected $scopeConfig;

    public function __construct(
      ScopeConfigInterface $scopeConfig
    ) {
          $this->scopeConfig = $scopeConfig;
    }


    public function getConfigValue($path)
    {
      return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    public function getEmarsysSettings()
    {
      return $this->getConfigValue(self::EMARSYS_LABEL_CONFIG_PATH);
    }

    public function send($requestType, $endPoint, $requestBody = '')
    {

        $emarsysSettings = $this->getEmarsysSettings();
        $url = false;
        $username= false;
        $password= false;

        if (isset($emarsysSettings['emarsys_custom_url'])) {
          $url = $emarsysSettings['emarsys_custom_url'];
        }
        if (isset($emarsysSettings['emarsys_api_username'])) {
          $username = $emarsysSettings['emarsys_api_username'];
        }
        if (isset($emarsysSettings['emarsys_api_password'])) {
          $password = $emarsysSettings['emarsys_api_password'];
        }
        if(!$url || !$username || !$password )
        {
          return $this;
        }
        $this->_username =$username;
        $this->_secret = $password;
        $this->_suiteApiUrl = $url;

        if (!in_array($requestType, array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new Exception('Send first parameter must be "GET", "POST", "PUT" or "DELETE"');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        switch ($requestType) {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                break;
        }
        curl_setopt($ch, CURLOPT_HEADER, true);

        $requestUri = $this->_suiteApiUrl . $endPoint;
        curl_setopt($ch, CURLOPT_URL, $requestUri);

        /**
         * We add X-WSSE header for authentication.
         * Always use random 'nonce' for increased security.
         * timestamp: the current date/time in UTC format encoded as
         *   an ISO 8601 date string like '2010-12-31T15:30:59+00:00' or '2010-12-31T15:30:59Z'
         * passwordDigest looks sg like 'MDBhOTMwZGE0OTMxMjJlODAyNmE1ZWJhNTdmOTkxOWU4YzNjNWZkMw=='
         */
        $nonce = $this->getRandomBytes();
        $timestamp = gmdate("c");

        $passwordDigest = base64_encode(sha1($nonce . $timestamp . $this->_secret, false));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-WSSE: UsernameToken ' .
                'Username="'.$this->_username.'", ' .
                'PasswordDigest="'.$passwordDigest.'", ' .
                'Nonce="'.$nonce.'", ' .
                'Created="'.$timestamp.'"',
                'Content-type: application/json;charset="utf-8"',
            ));

        $response = curl_exec($ch);
        curl_close($ch);

        $parts = preg_split("@\r?\n\r?\nHTTP/@u", $response);
        $parts = (count($parts) > 1 ? 'HTTP/' : '').array_pop($parts);
        list($headers, $output) = preg_split("@\r?\n\r?\n@u", $parts, 2);

        print_r($output);
    }
}
