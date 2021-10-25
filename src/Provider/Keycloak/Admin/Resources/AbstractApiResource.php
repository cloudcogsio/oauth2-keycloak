<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use League\OAuth2\Client\Token\AccessToken;

abstract class AbstractApiResource
{
    /** @var \Cloudcogs\OAuth2\Client\Provider\Keycloak **/
    protected $Keycloak;
    
    protected $ClientCredentialsToken;
    protected $endpoint;
    protected $resourceParams;
    
    public function __construct(Keycloak $Keycloak, string $endpoint)
    {
        $this->Keycloak = $Keycloak;
        
        $this->setEndpoint($endpoint);
    }
    
    protected function getAccessToken() : AccessToken
    {
        if(!$this->ClientCredentialsToken)
        {
            $this->ClientCredentialsToken = $this->Keycloak->getAccessToken("client_credentials");
        }
        
        return $this->ClientCredentialsToken;
    }
    
    protected final function setEndpoint(string $resourceEndpoint)
    {
        $this->endpoint = $this->Keycloak->getAdminApiBaseUrl().$resourceEndpoint;
        return $this;
    }
    
    protected final function getEndpoint() : string
    {
        return $this->endpoint;
    }
    
    protected function validateParams(array $params)
    {
        if (!$this->resourceParams)
        {
            $self = new \ReflectionClass($this);
            $this->resourceParams = array_flip($self->getConstants());
        }
        
        return array_intersect_key($params, $this->resourceParams);
    }
}
