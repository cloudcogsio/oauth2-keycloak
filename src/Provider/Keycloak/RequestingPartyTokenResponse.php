<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\TokenIntrospectionException;
use Laminas\Http\Response;
use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use Cloudcogs\OAuth2\Client\OpenIDConnect\ParsedToken;

class RequestingPartyTokenResponse extends Response
{
    CONST PARAM_TOKEN_TYPE_HINT = "token_type_hint";
    CONST TOKEN_TYPE_RPT = "requesting_party_token";
    
    protected RequestingPartyToken $RPT;
    protected Keycloak $Keycloak;
    protected ParsedToken $ParsedToken;

    /**
     * @param Keycloak $Keycloak
     * @param RequestingPartyToken $RequestingPartyToken
     * @param bool $useTokenHint
     * @throws TokenIntrospectionException
     */
    public function __construct(Keycloak $Keycloak, RequestingPartyToken $RequestingPartyToken, bool $useTokenHint = true)
    {
        $this->Keycloak = $Keycloak;
        $this->RPT = $RequestingPartyToken;
        
        if ($useTokenHint)
        {
            $this->ParsedToken = $Keycloak->introspectToken($RequestingPartyToken->getToken(), [
                self::PARAM_TOKEN_TYPE_HINT => self::TOKEN_TYPE_RPT
            ], false);
        } 
        else {
            $this->ParsedToken = $Keycloak->introspectToken($RequestingPartyToken->getToken());
        }
    }
    
    public function getRPT() : RequestingPartyToken
    {
        return $this->RPT;
    }
    
    public function getParsedToken() : ParsedToken
    {
        return $this->ParsedToken;
    }
    
    public function getPermissions() : ?array
    {
        return $this->getParsedToken()->permissions;
    }
}
