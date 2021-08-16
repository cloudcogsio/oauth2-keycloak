<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class RequestingPartyToken extends AccessToken
{
    public function isUpgraded()
    {
        return $this->queryValue("upgraded");
    }
        
    protected function queryValue(string $key)
    {
        if (array_key_exists($key, $this->getValues())) return $this->getValues()[$key];
        
        return false;
    }
}
