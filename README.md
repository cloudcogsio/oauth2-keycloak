# Keycloak Provider for OAuth 2.0 Client
![GitHub](https://img.shields.io/github/license/cloudcogsio/oauth2-keycloak) ![GitHub last commit](https://img.shields.io/github/last-commit/cloudcogsio/oauth2-keycloak)

This package provides Keycloak OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

The client uses Keycloak's ```.well-known``` services endpoint to query the OpenID Provider Metadata for autodiscovery of relevant endpoints for authorization, tokens and public keys for token introspection. 
 
## Installation
To install, use composer:

```
composer require cloudcogsio/oauth2-keycloak
```
## Usage
Usage is the same as The League's OAuth client, using `\Cloudcogs\OAuth2\Client\Provider\Keycloak` as the provider.

### Configuration via Keycloak OIDC JSON file
The client can be configured by passing the Keycloak OIDC JSON file that can be downloaded from your Keycloak server. 
1. Go to your Keycloak Admin
2. Select the "Clients" option
3. Select the Client ID of the required client
4. Select the "Installation" tab
5. In the "Format Option" dropdown, choose "Keycloak OIDC JSON"
6. Download. (Default filename is "keycloak.json")

When using the Keycloak OIDC JSON file, only the file and a redirectUri is required to setup the client.

#### Provider Configuration with Keycloak OIDC JSON (keycloak.json)
```php

$provider = new Keycloak([
    'config' => 'keycloak.json',
    'redirectUri' => 'https://example.com/callback-url'
]);

```
### Configuration via Options
The client can also be configured without a Keycloak OIDC JSON file by passing (at minimum) the ```authServerUrl``` and ```realm``` options required for endpoint autodiscovery. 

You will still need to reference the OIDC JSON configuration in Keycloak to retrieve the values for ```clientId``` and ```clientSecret```. These would be the ```resource``` and ```credentials->secret```.

#### Provider Configuration with ```authServerUrl``` and ```realm``` options
```php

$provider = new Keycloak([
    'authServerUrl' => 'http://localhost:8080/auth/',
    'realm' => 'demo-realm',
    'clientId' => '{keycloak-resource}',
    'clientSecret' => '{keycloak-credentials-secret}',
    'redirectUri' => 'https://example.com/callback-url'
]);

```

### Authorization Code Flow
Assuming ```$provider``` was configured as outlined via one of the methods above.
```php 

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; 
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }

    exit('Invalid state');
    
} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo 'Access Token: ' . $accessToken->getToken() . "<br>";
        echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
        echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
        echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        $request = $provider->getAuthenticatedRequest(
            'GET',
            'https://service.example.com/resource',
            $accessToken
        );

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }
}

```

### Refreshing a Token
```php

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    // Purge old access token and store new access token to your data store.
}

```

### Client Logout 
The client provides a method to conveniently process a logout action.

A redirect URI can be passed to the method or the ```redirectUri``` option of the client will be used for redirection. The URI must be configured in the "*Valid Redirect URIs*" field of the client definition in Keycloak.

```php

$url = "https://example.com/logout-url-redirect";
$provider->logoutAndRedirect($url);

```

### Resource Owner Password Credentials Grant
>🛑 **DANGER!** We advise against using this grant type if the service provider supports the authorization code grant type (see above), as this reinforces the [password anti-pattern](https://agentile.com/the-password-anti-pattern), allowing users to think it’s okay to trust third-party applications with their usernames and passwords.

That said, there are use-cases where the resource owner password credentials grant is acceptable and useful. 

```php

try {

    // Try to get an access token using the resource owner password credentials grant.
    $accessToken = $provider->getAccessToken('password', [
        'username' => 'myuser',
        'password' => 'mysupersecretpassword'
    ]);
    
	$resourceOwner = $provider->getResourceOwner($accessToken);
	
	var_export($resourceOwner->toArray());

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

    // Failed to get the access token
    exit($e->getMessage());

}

```
### Client Credentials Grant
When your application acts on its own behalf to access resources it controls or owns in a service provider, it may use the  _client credentials_  grant type.

The client credentials grant type is best when storing the credentials for your application privately and never exposing them (e.g., through the web browser, etc.) to end-users. This grant type functions like the resource owner password credentials grant type, but it does not request a user’s username or password. It uses only the client ID and client secret issued to your client by the service provider.

```php

try {

    // Try to get an access token using the client credentials grant.
    $accessToken = $provider->getAccessToken('client_credentials');

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

    // Failed to get the access token
    exit($e->getMessage());

}

```

## Additional Notes
### OpenID Connect Discovery endpoint
By default, this client uses the ```.well-known/openid-configuration``` endpoint to discover all other endpoints for the Keycloak server once the ```authServerUrl``` and ```realm``` options are supplied to create the client.

This is handled by the ```cloudcogsio\oauth2-openid-connect-discovery``` library. See https://github.com/cloudcogsio/oauth2-openid-connect-discovery


```php

// Get the discovered configurations from the provider instance
$discovered = $provider->Discovery();

// Access standard OpenID Connect configuration via supported methods
$issuer = $discovered->getIssuer();
$supported_grants = $discovered->getGrantTypesSupported();
$authorization_endpoint = $discovered->getAuthorizationEndpoint();

// Or overloading for Keycloak specific configuration
$check_session_iframe = $discovered->check_session_iframe;

// Cast to string to obtain the raw JSON discovery response
// All available properties for overloading can be seen in the JSON object.
$json_string = (string) $discovered;

```

### Keycloak Public Key(s)
During endpoint discovery, the Keycloak realm public key(s) are retrieved and cached locally. This is needed to decode the access token which is then added to the ``` \Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceOwner ``` object as additional values.

#### Caching of Public Keys
Caching of JWKs are handled by an instance of ```\Laminas\Cache\Storage\Adapter\FileSystem``` which is installed with ```cloudcogsio\oauth2-openid-connect-discovery```.

You can provide your own instance of a ```\Laminas\Cache\Storage\Adapter\*``` to handle storage of the Keycloak realm's public key.


### Token Introspection
By default, the accessToken is decoded locally using the cached public keys. Decoded data is populated and made available in the ```\Cloudcogs\OAuth2\Client\Provider\Keycloak\ResourceOwner``` object.

This is performed automatically by the client and requires no additional configuration. 

#### Token Introspection via Keycloak Server
All tokens issued by the Keycloak server (accessToken, refreshToken etc.) can be introspected using the Keycloak token introspection endpoint.

The client provides an ``` introspectToken(string $token)``` method to carry out this operation.

```php

// Decode the access token
$access_token = $AccessToken->getToken();
$data = $provider->introspectToken($access_token);

// Decode the refresh token
$refresh_token = $AccessToken->getRefreshToken();
$data = $provider->introspectToken($refresh_token);

```

## Custom Access Token Class
The [``` custom-access-token```](https://github.com/cloudcogsio/oauth2-keycloak/tree/custom-access-token) branch of this repository implements a custom ```\Cloudcogs\OAuth2\Client\Provider\Keycloak\AccessToken``` class that extends the base ``` \League\OAuth2\Client\Token\AccessToken ``` class. 

Keycloak provides a ```refresh_expires_in``` property This custom class adds additional methods that checks and detects the validity of the ```refreshToken```. The theory of operation is the same as that provided by the base class for checking and detecting the validity of the ```accessToken```. 

```AccessToken.php```
```php

namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use League\OAuth2\Client\Token\AccessToken as LeagueAccessToken;

class AccessToken extends LeagueAccessToken
{
    protected $refresh_expires;
    
    public function __construct(array $options)
    {
        parent::__construct($options);
        
        /**
         * Determine if the refresh token expires and set expiry time
         */
        if (array_key_exists("refresh_expires_in", $options)) 
        {
            if (!is_numeric($options['refresh_expires_in'])) {
                throw new \InvalidArgumentException('refresh_expires_in value must be an integer');
            }
            
            $this->refresh_expires = $options['refresh_expires_in'] != 0 ? $this->getTimeNow() + $options['refresh_expires_in'] : 0;
        }
    }
    
    public function getRefreshExpires()
    {
        return $this->refresh_expires;
    }
    
    public function hasRefreshExpired()
    {
        $expires = $this->getRefreshExpires();
        
        if (empty($expires)) {
            throw new \RuntimeException('"refresh_expires" is not set on the token');
        }
        
        return $expires < time();
    }
}

```
#### NOTE: At this time a custom AccessToken class is not supported by the base AbstractProvider class of ```thephpleague/oauth2-client```.

Method signature changes are required before custom Access Token classes (such as the one provided above) can be used.
See https://github.com/thephpleague/oauth2-client/issues/897


## License
The MIT License (MIT). Please see  [License File](https://github.com/cloudcogsio/oauth2-keycloak/blob/master/LICENSE.md)  for more information.
