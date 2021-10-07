<?php

namespace Paywant;

class Config
{
    private $apiKey;
    private $secretKey;
    private $serviceBaseUrl;
    private $sdkVersion = '1.0.0';

    public function getSDKVersion()
    {
        return $this->sdkVersion;
    }

    public function getAPIKey()
    {
        return $this->apiKey;
    }

    public function setAPIKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function getServiceBaseUrl()
    {
        return $this->serviceBaseUrl;
    }

    public function setServiceBaseUrl($serviceBaseUrl)
    {
        $this->serviceBaseUrl = $serviceBaseUrl;
    }
}
