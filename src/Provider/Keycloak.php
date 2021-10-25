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

namespace Cloudcogs\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\RequiredOptionMissingException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceOwner;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\InvalidConfigFileException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Config;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Cloudcogs\OAuth2\Client\OpenIDConnect\AbstractOIDCProvider;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Curl;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\AuthorizationTokenException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyToken;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenResponse;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceManagement;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\PermissionManagement;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Grants\TokenExchange;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\PolicyManagement;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources\AbstractApiResource;

class Keycloak extends AbstractOIDCProvider
{
    /**
     * 
     * @var string
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = "sub";
    
    /**
     * Key used in the $options array for passing in a 'keycloak.json' config file.
     */
    const OPTIONS_KEY_CONFIG = "config";
    
    /**
     * The Keycloak base URL
     * @example http://localhost:8080/auth
     *
     * @var string
     */
    protected $authServerUrl;
    
    /**
     * Auto-generated - Admin API base url for the admin API endpoints
     * @var string
     */
    protected $adminApiBaseUrl;
    
    /**
     * The Keycloak realm
     * 
     * @var string
     */
    protected $realm;
    
    /**
     * Keycloak JSON configuration (recommended but optional)
     * 
     * Pass 'config' key to $options array pointing to 'keycloak.json' configuration file.
     * 'keycloak.json' can be retrieved from the Keycloak server, 'Client->Installation' tab.
     * 
     * 
     * @var \Cloudcogs\OAuth2\Client\Provider\Keycloak\Config
     */
    protected $config;
    
    /**
     * Minimum required options for the constructor if no keycloak.json configuration file is provided.
     * These are used for autodiscovery of endpoints via the Keycloak well-known endpoint
     * 
     * @var array
     */
    protected $required = ['authServerUrl','realm'];
    
    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, and `httpClient`.
     *     Individual providers may introduce more collaborators, as needed.
     *     
     * For this Keycloak client, minimum required parameters for the constructor are
     * 'authServerUrl' and 'realm' if no 'keycloak.json' config file is provided.
     *     
     * @throws RequiredOptionMissingException
     */
    
    /** @var \Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceManagement **/
    protected $ResourceManagement;
    
    /** @var \Cloudcogs\OAuth2\Client\Provider\Keycloak\PermissionManagement **/
    protected $PermissionManagement;
    
    /** @var \Cloudcogs\OAuth2\Client\Provider\Keycloak\PolicyManagement **/
    protected $PolicyManagement;
    
    public function __construct(array $options = [], array $collaborators = [])
    {
        /**
         * First check for keycloak.json configuration file.
         */
        if (array_key_exists(self::OPTIONS_KEY_CONFIG, $options))
        {
            $this->loadKeycloakConfig($options[self::OPTIONS_KEY_CONFIG]);
        }
        
        // Config file not passed, check for authServerUrl and realm keys
        else 
        {
            /**
             * Check for required options for auto-discovery
             */
            foreach ($this->required as $param)
            {
                if (!array_key_exists($param, $options)) throw new RequiredOptionMissingException($param);
                
                // We need to set the required options if passed since they are used before the parent constructor is called.
                $this->$param = $options[$param];
            }
        }
        
        $options[AbstractOIDCProvider::OPTION_WELL_KNOWN_URL] = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/realms/".$this->realm."/.well-known/openid-configuration";
        $options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER] = (!isset($options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER])) ? '' : $options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER];
        
        $this->adminApiBaseUrl = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/admin/realms/".$this->realm."/";
        
        parent::__construct($options, $collaborators);
    }
    
    /**
     * Loads a 'keycloak.json file' containing the configuration required for interaction with the keycloak server.
     * 
     * @param string | array $config - Full path and filename of the configuration file or array representation of keycloak.json settings. 
     * @throws InvalidConfigFileException
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak
     */
    public function loadKeycloakConfig($config)
    {
        if (is_array($config))
        {
            $json = $config;
        }
        else
        {
            if (file_exists($config))
            {
                $json = json_decode(file_get_contents($config),JSON_OBJECT_AS_ARRAY);
            }
        }
        
        if (is_array($json))
        {
            $this->config = new Config($json);
            
            $this->realm = $this->config->getRealm();
            $this->authServerUrl = $this->config->getAuthServerUrl();
            $this->clientId = $this->config->getClientId();
            $this->clientSecret = $this->config->getClientSecret();
            
            return $this;
        }
        
        throw new InvalidConfigFileException($config);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getDefaultScopes()
     */
    protected function getDefaultScopes() : array
    {
        return ['openid','profile','email'];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getScopeSeparator()
     */
    protected function getScopeSeparator() : string
    {
        return ' ';
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::checkResponse()
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (array_key_exists('error', $data))
        {
            throw new IdentityProviderException(@$data['error']." [".@$data['error_description']."]", $response->getStatusCode(), $data);
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::createResourceOwner()
     *
     * @return ResourceOwner
     */
    protected function createResourceOwner(array $response, AccessTokenInterface $token) : ResourceOwner
    {
        return new ResourceOwner($response, $token, $this);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getAuthorizationHeaders()
     *
     * @return array
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return [
            'Authorization' => "Bearer $token"
        ];
    }
    
    /**
     * (Convenience Method) Returns the Keycloak introspection endpoint that can be used to obtain additional information about a token.
     * By default, this client will perform local validation and decoding using the realm's cached public key (retrieved during endpoints autodiscovery) and returns the additional information in the getResourceOwner() response.
     * Using the introspection endpoint performs the validation and decoding on the Keycloak server.
     * 
     * @return string
     */
    public function getIntrospectionEndpoint()
    {
        return $this->OIDCDiscovery->getIntrospectionEndpoint();
    }
    
    /**
     * (Convenience Method) Returns the Keycloak logout URL
     * 
     * @return string
     */
    public function getEndSessionEndpoint()
    {
        return $this->OIDCDiscovery->end_session_endpoint;
    }
    
    /**
     * (Convenience Method) Proxy to getEndSessionEndpoint()
     * @see Keycloak::getEndSessionEndpoint()
     * 
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->getEndSessionEndpoint();
    }
    
    /**
     * (Convenience Method) Perform keycloak logout and redirect
     * Implementions will need to clear application session accordingly. 
     * 
     * @param string $redirect_url - Redirect URL after logout. If none is provided, the configured provider 'redirectUrl' will be used 
     */
    public function logoutAndRedirect(string $redirect_uri = null)
    {
        if (is_null($redirect_uri)) $redirect_uri = $this->redirectUri;
        header("Location: ".$this->getLogoutUrl()."?redirect_uri=".$redirect_uri);
        exit;
    }
    
    /**
     * The certificate endpoint returns the public keys enabled by the realm, encoded as a JSON Web Key (JWK). 
     * Depending on the realm settings there can be one or more keys enabled for verifying tokens. 
     * For more information see the Keycloak Server Administration Guide and the JSON Web Key specification.
     * 
     * @return string
     */
    public function getCertificateEndpoint()
    {
        return $this->OIDCDiscovery->getJwksUri();
    }
    
    /**
     * For Keycloak clients that are configured for Authorization, this method retrieves a token from Keycloak that contains the resource permissions for the user.
     * Additional features are also effected based on the RequestingPartyTokenRequest that is passed to this method.
     *
     * @see \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     * @see https://www.keycloak.org/docs/latest/authorization_services/#_service_rpt_overview
     *
     * @param RequestingPartyTokenRequest $Request
     * @param bool $useTokenHint - Indicates if the RPT should contain mainly the "permissions" (true) claim or the full UMA token (false). 
     * @throws IdentityProviderException
     * @throws AuthorizationTokenException
     *
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     */
    public function getAuthorizationToken(RequestingPartyTokenRequest $Request, bool $useTokenHint = true) : RequestingPartyTokenResponse
    {
        $Client = new Client(null,[
            'adapter'     => Curl::class,
            'curloptions' => [
                CURLOPT_RETURNTRANSFER => true,
            ]
        ]);
        
        $Response = $Client->send($Request);
        
        $AccessToken = json_decode((string) $Response->getBody(), true);
        
        if (is_array($AccessToken))
        {
            switch ($Response->getStatusCode())
            {
                case "200":
                    return new RequestingPartyTokenResponse($this, new RequestingPartyToken($AccessToken), $useTokenHint);
                    break;
                    
                default:
                    throw new IdentityProviderException(@$AccessToken['error']." [".@$AccessToken['error_description']."]", $Response->getStatusCode(), $AccessToken);
                    break;
            }
        }
        
        throw new AuthorizationTokenException();
    }

    /**
     * Exchange a token issued by another client
     * 
     * @see https://www.keycloak.org/docs/latest/securing_apps/#_token-exchange
     * 
     * @param string $ClientAccessToken - Bearer Token issued by another client
     * @param string $requested_token_type -  If your requested_token_type parameter is a refresh token type, then the response will contain both an access token, refresh token, and expiration.
     * @return AccessTokenInterface
     */
    public function tokenExchange(string $ClientAccessToken, string $requested_token_type = TokenExchange::REQUESTED_TOKEN_TYPE_ACCESS) : AccessTokenInterface
    {
        $TokenExchangeGrant = new TokenExchange();
        return $this->getAccessToken($TokenExchangeGrant, [
            'subject_token' => $ClientAccessToken,
            'audience' => $this->clientId,
            'requested_token_type' => $requested_token_type
        ]);
    }

    /**
     * Proxy to Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceManagement
     * 
     * @param object $WellKnownUMA2Configuration - Previously retrieved UMA well-known config. If empty, autodiscovery is performed.
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceManagement
     */
    public function ResourceManagement($WellKnownUMA2Configuration = null) : ResourceManagement
    {
        if (!$this->ResourceManagement)
        {
            $uma2_url = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/realms/".$this->realm."/.well-known/uma2-configuration";
            
            if ($WellKnownUMA2Configuration == null)
            {
                $resource_registration_endpoint = "";
            }
            else {
                $resource_registration_endpoint = $WellKnownUMA2Configuration->resource_registration_endpoint;
            }
            
            $this->ResourceManagement = new ResourceManagement($this, $uma2_url, (empty($resource_registration_endpoint)) ? true : false);
            
            if ($WellKnownUMA2Configuration)
            {
                $this->ResourceManagement->setWellKnownConfiguration($WellKnownUMA2Configuration);
            }
        }
        
        return $this->ResourceManagement;
    }
    
    /**
     * Proxy to Cloudcogs\OAuth2\Client\Provider\Keycloak\PermissionManagement
     * 
     * @param object $WellKnownUMA2Configuration - Previously retrieved UMA well-known config. If empty, autodiscovery is performed.
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\PermissionManagement
     */
    public function PermissionManagement($WellKnownUMA2Configuration = null) : PermissionManagement
    {
        if (!$this->PermissionManagement)
        {
            $uma2_url = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/realms/".$this->realm."/.well-known/uma2-configuration";
            
            if ($WellKnownUMA2Configuration == null)
            {
                $permission_endpoint = "";
            }
            else {
                $permission_endpoint = $WellKnownUMA2Configuration->permission_endpoint;
            }
            
            $this->PermissionManagement = new PermissionManagement($this, $uma2_url, (empty($permission_endpoint)) ? true : false);
            
            if ($WellKnownUMA2Configuration)
            {
                $this->PermissionManagement->setWellKnownConfiguration($WellKnownUMA2Configuration);
            }
        }
        
        return $this->PermissionManagement;
    }
    
    /**
     * Proxy to Cloudcogs\OAuth2\Client\Provider\Keycloak\PolicyManagement
     * 
     * @param object $WellKnownUMA2Configuration - Previously retrieved UMA well-known config. If empty, autodiscovery is performed.
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\PolicyManagement
     */
    public function PolicyManagement(string $ClientAccessToken, $WellKnownUMA2Configuration = null) : PolicyManagement
    {
        if (!$this->PolicyManagement)
        {
            $uma2_url = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/realms/".$this->realm."/.well-known/uma2-configuration";
            
            if ($WellKnownUMA2Configuration == null)
            {
                $policy_endpoint = "";
            }
            else {
                $policy_endpoint = $WellKnownUMA2Configuration->policy_endpoint;
            }
            
            $this->PolicyManagement = new PolicyManagement($this, $uma2_url, (empty($policy_endpoint)) ? true : false);
            $this->PolicyManagement->setUMAPolicyAccessToken($this->tokenExchange($ClientAccessToken));
            
            if ($WellKnownUMA2Configuration)
            {
                $this->PolicyManagement->setWellKnownConfiguration($WellKnownUMA2Configuration);
            }
        }
        
        return $this->PolicyManagement;
    }
    
    /**
     * Returns an instance of the specified resource Api.
     * Resources can be one of the RESOURCE_* constants defined in \Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory
     * 
     * @param string $Resource
     * @return AbstractApiResource
     */
    public function AdminApiClientFactory(string $Resource) : AbstractApiResource
    {
        return (new ClientFactory())($this, $Resource);
    }
    
    public function getAdminApiBaseUrl()
    {
        return $this->adminApiBaseUrl;
    }
}

