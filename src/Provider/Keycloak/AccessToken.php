<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use League\OAuth2\Client\Token\AccessToken as LeagueAccessToken;

class AccessToken extends LeagueAccessToken
{
    protected $refresh_expires;
    
    public function __construct(array $options)
    {
        parent::__construct($options);
        
        /**
         * Determine if the refresh token expires and set expiry time
         */
        if (array_key_exists("refresh_expires_in", $options)) 
        {
            if (!is_numeric($options['refresh_expires_in'])) {
                throw new \InvalidArgumentException('refresh_expires_in value must be an integer');
            }
            
            $this->refresh_expires = $options['refresh_expires_in'] != 0 ? $this->getTimeNow() + $options['refresh_expires_in'] : 0;
        }
    }
    
    public function getRefreshExpires()
    {
        return $this->refresh_expires;
    }
    
    public function hasRefreshExpired(): bool
    {
        $expires = $this->getRefreshExpires();
        
        if (empty($expires)) {
            throw new \RuntimeException('"refresh_expires" is not set on the token');
        }
        
        return $expires < time();
    }
}
