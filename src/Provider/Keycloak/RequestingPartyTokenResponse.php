<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Laminas\Http\Response;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyToken;
use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use Cloudcogs\OAuth2\Client\OpenIDConnect\ParsedToken;

class RequestingPartyTokenResponse extends Response
{
    protected $RPT;
    protected $Keycloak;
    protected $ParsedToken;
    
    public function __construct(Keycloak $Keycloak, RequestingPartyToken $RequestingPartyToken)
    {
        $this->Keycloak = $Keycloak;
        $this->RPT = $RequestingPartyToken;
        
        $this->ParsedToken = $Keycloak->introspectToken($RequestingPartyToken->getToken());
    }
    
    public function getParsedToken() : ParsedToken
    {
        return $this->ParsedToken;
    }
    
    public function getPermissions()
    {
        return $this->getParsedToken()->authorization->permissions;
    }
}
