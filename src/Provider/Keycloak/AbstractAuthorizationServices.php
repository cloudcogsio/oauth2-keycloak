<?php
/**
 * Copyright 2021, Cloudcogs.io
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use League\OAuth2\Client\Provider\AbstractProvider;
use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\InvalidUrlException;
use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\WellKnownEndpointException;

abstract class AbstractAuthorizationServices
{
    private $uma2_well_known_url;
    private $WellKnownUMA2Configuration;
    private $PAT;
    
    protected $Keycloak;
    
    public function __construct(AbstractProvider $Keycloak, string $oidc_well_known_url, bool $discover = false)
    {
        $this->uma2_well_known_url = str_replace("openid", "uma2", $oidc_well_known_url);
        $this->Keycloak = $Keycloak;
        
        if ($discover) {
            $this->getWellKnownUMA2Endpoints();
        }
    }
    
    public function getProtectionAPIToken()
    {
        return (!$this->PAT)? $this->PAT = $this->Keycloak->getAccessToken('client_credentials') : $this->PAT;
    }
    
    public function getWellKnownUMA2Endpoints()
    {
        // Check if well-known URL has a valid URL format
        if(!filter_var($this->uma2_well_known_url, FILTER_VALIDATE_URL))
            throw new InvalidUrlException($this->uma2_well_known_url);
            
            
        // Build the HTTPRequest
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest(AbstractProvider::METHOD_GET, $this->uma2_well_known_url);
           
        // Execute discovery request
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
           
        if ($HttpResponse->getStatusCode() == "200")
        {
            $this->WellKnownUMA2Configuration = (object) json_decode((string) $HttpResponse->getBody());
        }
        else
        {
            throw new WellKnownEndpointException($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    /**
     * Get the well-known configuration as an object
     *
     * @return object
     */
    public function getWellKnownConfiguration()
    {
        return $this->WellKnownUMA2Configuration;
    }
    
    public function setWellKnownConfiguration($WellKnownUMA2Configuration)
    {
        $this->WellKnownUMA2Configuration = $WellKnownUMA2Configuration;
        return $this;
    }
    
    public function getIssuer()
    {
        return $this->issuer;
    }
    
    public function getAuthorizationEndpoint()
    {
        return $this->authorization_endpoint;
    }
    
    public function getTokenEndpoint()
    {
        return $this->token_endpoint;
    }
    
    public function getIntrospectionEndpoint()
    {
        return $this->introspection_endpoint;
    }
    
    public function getEndSessionEndpoint()
    {
        return $this->end_session_endpoint;
    }
    
    public function getJwksUri()
    {
        return $this->jwks_uri;
    }
    
    public function getGrantTypesSupported()
    {
        return $this->grant_types_supported;
    }
    
    public function getResponseTypesSupported()
    {
        return $this->response_types_supported;
    }
    
    public function getResponseModesSupported()
    {
        return $this->response_modes_supported;
    }
    
    public function getRegistrationEndpoint()
    {
        return $this->registration_endpoint;
    }
    
    public function getTokenEndpointAuthMethodsSupported()
    {
        return $this->token_endpoint_auth_methods_supported;
    }
    
    public function getTokenEndpointAuthSigningAlgValuesSupported()
    {
        return $this->token_endpoint_auth_signing_alg_values_supported;
    }
    
    public function getScopesSupported()
    {
        return $this->scopes_supported;
    }
    
    public function getResourceRegistrationEndpoint()
    {
        return $this->resource_registration_endpoint;
    }
    
    public function getPermissionEndpoint()
    {
        return $this->permission_endpoint;
    }
    
    public function getPolicyEndpoint()
    {
        return $this->policy_endpoint;
    }
    
    public function __get($property)
    {
        if (property_exists($this->WellKnownUMA2Configuration, $property))
        {
            return $this->WellKnownUMA2Configuration->$property;
        }
        
        return null;
    }
    
    public function __toString()
    {
        return json_encode($this->WellKnownUMA2Configuration);
    }
}
