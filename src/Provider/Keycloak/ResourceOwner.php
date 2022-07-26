<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Cloudcogs\OAuth2\Client\OpenIDConnect\AbstractOIDCProvider;

class ResourceOwner implements ResourceOwnerInterface
{
    protected array $response;

    public function __construct(array $response, AccessTokenInterface $token, AbstractOIDCProvider $Provider)
    {
        $parsedToken = $Provider->introspectToken($token->getToken());
        
        if ($parsedToken instanceof \Cloudcogs\OAuth2\Client\OpenIDConnect\ParsedToken)
        {
            $this->response = array_merge($response, $parsedToken->toArray());
        }
        else
        {
            $this->response = $response;
        }
    }

    public function toArray(): array
    {
        return $this->response;
    }

    public function getId()
    {
        return (array_key_exists(Keycloak::ACCESS_TOKEN_RESOURCE_OWNER_ID, $this->response)) ? $this->response[Keycloak::ACCESS_TOKEN_RESOURCE_OWNER_ID] : null; 
    }
    
    public function isEmailVerified()
    {
        return $this->response['email_verified'];
    }
    
    public function getName()
    {
        return $this->response['name'];
    }
    
    public function getPreferredUsername()
    {
        return $this->response['preferred_username'];
    }
    
    public function getGivenName()
    {
        return $this->response['given_name'];
    }
    
    public function getFamilyName()
    {
        return $this->response['family_name'];
    }
    
    public function getEmail()
    {
        return $this->response['email'];
    }
    
    public function __get($key)
    {
        if (array_key_exists($key, $this->response))
        {
            return $this->response[$key];
        }
        
        return null;
    }
}
