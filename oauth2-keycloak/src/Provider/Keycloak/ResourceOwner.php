<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class ResourceOwner implements ResourceOwnerInterface
{
    protected $response;

    public function __construct(array $response, AccessTokenInterface $token, $publicKey = null)
    {
        $this->response = $response;
        
        if ($publicKey)
        {
            $this->introspectToken($token, $publicKey);
        }
    }

    public function toArray()
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
    
    protected function introspectToken(AccessTokenInterface $token, $publicKey)
    {
        try {
            $data = JWT::decode($token->getToken(), JWK::parseKeySet($publicKey),[$publicKey['keys'][0]['alg']]);
            $this->response = array_merge($this->response, (array) $data);

        } catch (\Exception $e){
            // Token introspection failed
        }
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
