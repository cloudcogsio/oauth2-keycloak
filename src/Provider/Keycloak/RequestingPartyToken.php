<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class RequestingPartyToken extends AccessToken
{
    protected bool $isAccessToken = false;
    protected bool $resultDecision = false;
    protected array $permissions = [];

    // If "setResponseMode()" is used on the RPTRequest, an actual JWT is NOT returned.
    // We should not call the parent constructor since $options will NOT have 'access_token'
    // Instead, $options at this point has a list of permissions, or a decision result from KC.
    public function __construct(array $options)
    {
        if (isset($options['access_token'])){
            $this->isAccessToken = true;
            parent::__construct($options);
        } else {
            // If RPTRequest response mode is 'permission'
            if (!isset($options['result'])) {
                $this->permissions = $options;
            }

            // If RPTRequest response mode is 'decision'
            else {
                $this->resultDecision = (bool) $options['result'];
            }
        }
    }

    public function getDecision() : bool {
        return $this->resultDecision;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

    public function isAccessToken(): bool
    {
        return $this->isAccessToken;
    }

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
