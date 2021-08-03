<?php
/**
 * Keycloak OpenIDConnectDiscovery Class
 * 
 * Queries the OpenID Connect well-known endpoint for endpoints and other configuration options relevant to the OpenID Connect implementation in Keycloak.
 * Tested with Keycloak Version 13.0.1
 *
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\WellKnownEndpointException;
use GuzzleHttp\Psr7\Request;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\CertificateEndpointException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface;

class OpenIDConnectDiscovery
{
    protected $well_known_endpoint;
    protected $well_known_config;
    protected $key_data;
    
    /** @var \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface **/
    protected $PublicKeyCacheDriver;
    
    /**
     * Create and request well-known endpoint URL
     * 
     * @param string $authServerUrl
     * @param string $realm
     */
    public function __construct($authServerUrl, $realm, PublicKeyCacheInterface $PublicKeyCacheDriver)
    {
        $this->well_known_endpoint = ((substr($authServerUrl,-1) == "/") ? rtrim($authServerUrl,"/") : $authServerUrl)."/realms/".$realm."/.well-known/openid-configuration";
        $this->discoverEndpoints();
        
        $this->PublicKeyCacheDriver = $PublicKeyCacheDriver;
        $this->cachePublicKey();
    }
    
    protected function discoverEndpoints()
    {
        $HttpRequest = new Request("GET", $this->well_known_endpoint);
        
        /** @var $HttpResponse \GuzzleHttp\Psr7\Response **/
        $HttpResponse = (new Client())->sendRequest($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $this->well_known_config = (object) json_decode((string) $HttpResponse->getBody());
            
            return $this;
        }
        else 
        {
            throw new WellKnownEndpointException($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    /**
     * Retrieve and cache the JWKs from Keycloak
     * 
     * @param bool $clearCache - true will clear the cached keys and retrieve again from keycloak
     * @throws CertificateEndpointException
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\OpenIDConnectDiscovery
     */
    protected function cachePublicKey($clearCache = false)
    {
        if ($clearCache)
        {
            $this->PublicKeyCacheDriver->clear();
        }
        else 
        {
            $keys = $this->PublicKeyCacheDriver->load();
            
            /**
             * If keys are cached, load and return
             */
            if ($keys) 
            {
                $this->key_data = $keys;
                return $this;
            }
        }
        
        $HttpRequest = new Request("GET", $this->well_known_config->jwks_uri);
        
        /** @var $HttpResponse \GuzzleHttp\Psr7\Response **/
        $HttpResponse = (new Client())->sendRequest($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $keys = (array) json_decode((string) $HttpResponse->getBody(), true);
            
            $this->key_data = $keys;
            
            $this->PublicKeyCacheDriver->save($keys);
            
            return $this;
        }
        else
        {
            throw new CertificateEndpointException($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    /**
     * Return well-known endpoint configuration data
     * 
     * @return object
     */
    public function getConfig()
    {
        return $this->well_known_config;
    }

    /**
     * Returns the realm public key data
     * 
     * @return object
     */
    public function getPublicKey()
    {
        return $this->key_data;
    }
    
    /**
     * Clear the Public Key cache
     * 
     * @param array $options
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\OpenIDConnectDiscovery
     */
    public function clearPublicKeyCache(array $options = [])
    {
        $this->PublicKeyCacheDriver->clear($options);
        return $this;
    }
    
    /**
     * 
     * @param string $endpoint
     * @return string | array
     */
    public function __get($endpoint)
    {
        if (property_exists($this->well_known_config, $endpoint))
        {
            return $this->well_known_config->$endpoint;
        }
    }
    
    /**
     * JSON data returned from the well-known endpoint
     * 
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->well_known_config);
    }
}
