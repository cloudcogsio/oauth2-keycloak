<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin;

use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiResourceException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiResourceNotFoundException;

class ClientFactory
{
    const RESOURCE_GROUPS = "Groups";
    const RESOURCE_USERS = "Users";
    
    protected $validatedResources;
    
    protected $endpoints = [
        self::RESOURCE_GROUPS => "groups",
        self::RESOURCE_USERS => "users"
    ];
    
    function __invoke(Keycloak $Keycloak, string $Resource)
    {        
        $this->validateResource($Resource);
        
        $className = "\\Cloudcogs\\OAuth2\\Client\\Provider\\Keycloak\\Admin\\Resources\\".$Resource;
        if (class_exists($className, true))
        {
            return new $className($Keycloak, $this->endpoints[$Resource]);
        }
        
        throw new ApiResourceNotFoundException($Resource);
    }
    
    private function validateResource($Resource)
    {
        if (!$this->validatedResources)
        {
            $self = new \ReflectionClass($this);
            $this->validatedResources = array_flip($self->getConstants());
        }
        
        if(!array_key_exists($Resource, $this->validatedResources))
        {
            throw new ApiResourceException();
        }
    }
}
