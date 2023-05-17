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
use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class AbstractAuthorizationServices
{
    private string|array $uma2_well_known_url;
    private object $WellKnownUMA2Configuration;
    private AccessTokenInterface $PAT;
    
    protected Keycloak $Keycloak;

    /**
     * @param Keycloak $Keycloak
     * @param string $oidc_well_known_url
     * @param bool $discover
     * @throws InvalidUrlException
     * @throws WellKnownEndpointException
     */
    public function __construct(Keycloak $Keycloak, string $oidc_well_known_url, bool $discover = false)
    {
        $this->uma2_well_known_url = str_replace("openid", "uma2", $oidc_well_known_url);
        $this->Keycloak = $Keycloak;
        
        if ($discover) {
            $this->getWellKnownUMA2Endpoints();
        }
    }

    /**
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     */
    public function getProtectionAPIToken() : AccessTokenInterface
    {
        return (!isset($this->PAT))? $this->PAT = $this->Keycloak->getAccessToken('client_credentials') : $this->PAT;
    }

    /**
     * @return object
     * @throws InvalidUrlException
     * @throws WellKnownEndpointException
     */
    public function getWellKnownUMA2Endpoints(): object
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
            return $this->WellKnownUMA2Configuration;
        }

        throw new WellKnownEndpointException($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }
    
    /**
     * Get the well-known configuration as an object
     *
     * @return object
     */
    public function getWellKnownConfiguration(): object
    {
        return $this->WellKnownUMA2Configuration;
    }
    
    public function setWellKnownConfiguration(object $WellKnownUMA2Configuration): AbstractAuthorizationServices
    {
        $this->WellKnownUMA2Configuration = $WellKnownUMA2Configuration;
        return $this;
    }

    public function getIssuer() : string
    {
        return $this->issuer;
    }
    
    public function getAuthorizationEndpoint() : string
    {
        return $this->authorization_endpoint;
    }
    
    public function getTokenEndpoint() : string
    {
        return $this->token_endpoint;
    }
    
    public function getIntrospectionEndpoint() : string
    {
        return $this->introspection_endpoint;
    }
    
    public function getEndSessionEndpoint() : string
    {
        return $this->end_session_endpoint;
    }

    public function isFrontChannelLogoutSessionSupported() : bool
    {
        return $this->frontchannel_logout_session_supported;
    }

    public function isFrontChannelLogoutSupported() : bool
    {
        return $this->frontchannel_logout_supported;
    }
    
    public function getJwksUri() : string
    {
        return $this->jwks_uri;
    }
    
    public function getGrantTypesSupported() : array
    {
        return $this->grant_types_supported;
    }
    
    public function getResponseTypesSupported() : array
    {
        return $this->response_types_supported;
    }
    
    public function getResponseModesSupported() : array
    {
        return $this->response_modes_supported;
    }
    
    public function getRegistrationEndpoint() : string
    {
        return $this->registration_endpoint;
    }
    
    public function getTokenEndpointAuthMethodsSupported() : array
    {
        return $this->token_endpoint_auth_methods_supported;
    }
    
    public function getTokenEndpointAuthSigningAlgValuesSupported() : array
    {
        return $this->token_endpoint_auth_signing_alg_values_supported;
    }
    
    public function getScopesSupported() : array
    {
        return $this->scopes_supported;
    }
    
    public function getResourceRegistrationEndpoint() : string
    {
        return $this->resource_registration_endpoint;
    }
    
    public function getPermissionEndpoint() : string
    {
        return $this->permission_endpoint;
    }
    
    public function getPolicyEndpoint() : string
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
