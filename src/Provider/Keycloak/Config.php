<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class Config
{
    const CONFIG_KEY_REALM = 'realm';
    const CONFIG_KEY_AUTHSERVERURL = 'auth-server-url';
    const CONFIG_KEY_SSLREQUIRED = 'ssl-required';
    const CONFIG_KEY_RESOURCE = 'resource';
    const CONFIG_KEY_VERIFYTOKENAUDIENCE = 'verify-token-audience';
    const CONFIG_KEY_CREDENTIALS = 'credentials';
    const CONFIG_KEY_SECRET = 'secret';
    const CONFIG_KEY_CONFIDENTIALPORT = 'confidential-port';
    const CONFIG_KEY_POLICYENFORCER = 'policy-enforcer';
    
    protected array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function getRealm() : ?string
    {
        return @$this->config[self::CONFIG_KEY_REALM];
    }
    
    public function getAuthServerUrl() : ?string
    {
        return @$this->config[self::CONFIG_KEY_AUTHSERVERURL];
    }
    
    public function getSslRequired() : ?string
    {
        return @$this->config[self::CONFIG_KEY_SSLREQUIRED];
    }
    
    public function getClientId() : ?string
    {
        return @$this->config[self::CONFIG_KEY_RESOURCE];
    }
    
    public function getClientSecret() : ?string
    {
        $credentials = $this->getCredentials();
        return @$credentials[self::CONFIG_KEY_SECRET];
    }
    
    public function getCredentials() : array
    {
        return (array) @$this->config[self::CONFIG_KEY_CREDENTIALS];
    }
    
    public function getConfidentialPort() : ?int
    {
        return intval(@$this->config[self::CONFIG_KEY_CONFIDENTIALPORT]);
    }
    
    public function getPolicyEnforcer() : ?array
    {
        return (array) @$this->config[self::CONFIG_KEY_POLICYENFORCER];
    }
}
