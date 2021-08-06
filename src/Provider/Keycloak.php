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
     * Key used in the $options array for passing in an object implementing the \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface
     * This is used to cache the keycloak realm public key.
     * 
     * If not provided, the builtin 'file' driver will be used.
     * @see \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\File
     */
    const OPTIONS_KEY_PUBLIC_KEY_CACHE_DRIVER = "cache_driver";
    
    /**
     * The Keycloak base URL
     * @example http://localhost:8080/auth
     *
     * @var string
     */
    protected $authServerUrl;
    
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
    protected $required = ['authServerUrl','realm',AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER];
    
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
            }
        }
        
        $options[AbstractOIDCProvider::OPTION_WELL_KNOWN_URL] = ((substr($this->authServerUrl,-1) == "/") ? rtrim($this->authServerUrl,"/") : $this->authServerUrl)."/realms/".$this->realm."/.well-known/openid-configuration";
        $options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER] = (!isset($options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER])) ? '' : $options[AbstractOIDCProvider::OPTION_PUBLICKEY_CACHE_PROVIDER];
        
        parent::__construct($options, $collaborators);
    }
    
    /**
     * Loads a 'keycloak.json file' containing the configuration required for interaction with the keycloak server.
     * 
     * @param string $file Full path and filename of the configuration file
     * @throws InvalidConfigFileException
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak
     */
    public function loadKeycloakConfig($file)
    {
        if (file_exists($file))
        {
            $json = json_decode(file_get_contents($file),JSON_OBJECT_AS_ARRAY);
            if (is_array($json))
            {
                $this->config = new Config($json);
                
                $this->realm = $this->config->getRealm();
                $this->authServerUrl = $this->config->getAuthServerUrl();
                $this->clientId = $this->config->getClientId();
                $this->clientSecret = $this->config->getClientSecret();
                
                return $this;
            }
        }
        
        throw new InvalidConfigFileException($file);
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
            throw new IdentityProviderException(@$data['error']." [".@$data['error_response']."]", $response->getStatusCode(), $data);
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::createResourceOwner()
     */
    protected function createResourceOwner(array $response, AccessTokenInterface $token) : ResourceOwner
    {
        return new ResourceOwner($response, $token, $this);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getAuthorizationHeaders()
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return [
            'Authorization' => "Bearer $token"
        ];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getResourceOwnerDetailsUrl()
     */
    public function getResourceOwnerDetailsUrl(AccessTokenInterface $token) : string
    {
        return $this->OIDCDiscovery->getUserInfoEndpoint();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getBaseAuthorizationUrl()
     */
    public function getBaseAuthorizationUrl() : string
    {
        return $this->OIDCDiscovery->getAuthorizationEndpoint();
    }

    /**
     * 
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getBaseAccessTokenUrl()
     */
    public function getBaseAccessTokenUrl(array $params) : string
    {
        return $this->OIDCDiscovery->getTokenEndpoint();
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
     */
    public function getCertificateEndpoint()
    {
        return $this->OIDCDiscovery->getJwksUri();
    }
}

