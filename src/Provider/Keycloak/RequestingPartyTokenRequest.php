<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Cloudcogs\OAuth2\Client\OpenIDConnect\Grants\UmaTicket;
use Laminas\Http\Request;
use Cloudcogs\OAuth2\Client\Provider\Keycloak;

class RequestingPartyTokenRequest extends Request
{
    public function __construct(Keycloak $Keycloak, string $AccessToken)
    {
        $this->setMethod(self::METHOD_POST);
        $this->setUri($Keycloak->Discovery()->getTokenEndpoint());
        $this
            ->getHeaders()
                ->addHeaderLine('Content-Type','application/x-www-form-urlencoded')
                ->addHeaderLine('Accept','application/json')
                ->addHeaderLine('Authorization','Bearer '.$AccessToken);
        
        $this->getPost()->set('grant_type', (new UmaTicket())->__toString());
    }
    
    /**
     * This parameter is optional.
     * The most recent permission ticket received by the client as part of the UMA authorization process.
     *
     * @param string $ticket
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setTicket(string $ticket)
    {
        $this->getPost()->set('ticket', $ticket);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A string representing additional claims that should be considered by the server when evaluating permissions for the resource(s) and scope(s) being requested.
     * This parameter allows clients to push claims to Keycloak.
     *
     * @see https://www.keycloak.org/docs/latest/authorization_services/
     *
     * @param ClaimToken $ClaimToken
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setClaimToken(ClaimToken $ClaimToken)
    {
        $this->getPost()->get('claim_token_format', $ClaimToken::FORMAT);
        $this->getPost()->get('claim_token', $ClaimToken->__toString());
        
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A previously issued RPT which permissions should also be evaluated and added in a new one.
     * This parameter allows clients in possession of an RPT to perform incremental authorization where permissions are added on demand.
     *
     * @param string $rpt
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setRpt(string $rpt)
    {
        $this->getPost()->set('rpt', $rpt);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A string representing a set of one or more resources and scopes the client is seeking access.
     * This parameter can be defined multiple times in order to request permission for multiple resource and scopes.
     * This parameter is an extension to urn:ietf:params:oauth:grant-type:uma-ticket grant type in order to allow clients
     * to send authorization requests without a permission ticket.
     *
     * The format of the string must be: RESOURCE_ID#SCOPE_ID.
     * For instance: Resource A#Scope A, Resource A#Scope A, Scope B, Scope C, Resource A, #Scope A.
     *
     * @param string $permission
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function addPermission(string $permission)
    {
        $permission = $this->getPost('permission');
        if(!is_array($permission))
        {
            $permission = [$permission];
        }
        $this->getPost()->set('permission', $permission);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * The client identifier of the resource server to which the client is seeking access.
     * This parameter is mandatory in case the permission parameter is defined.
     * It serves as a hint to Keycloak to indicate the context in which permissions should be evaluated.
     *
     * @param string $audience
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setAudience(string $audience)
    {
        $this->getPost()->set('audience', $audience);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A boolean value indicating to the server whether resource names should be included in the RPT’s permissions.
     * If false, only the resource identifier is included.
     *
     * @param bool $bool
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function includeResourceNameInResponse(bool $bool = true)
    {
        $this->getPost()->set('response_include_resource_name', $bool);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * An integer N that defines a limit for the amount of permissions an RPT can have.
     * When used together with rpt parameter, only the last N requested permissions will be kept in the RPT.
     *
     * @param int $limit
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setPermissionsLimit(int $limit)
    {
        $this->getPost()->set('response_permissions_limit', $limit);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A boolean value indicating whether the server should create permission requests to the resources and scopes referenced by a permission ticket.
     * This parameter only has effect if used together with the ticket parameter as part of a UMA authorization process.
     *
     * @param bool $bool
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function submitUMARequest(bool $bool = true)
    {
        $this->getPost()->set('submit_request', $bool);
        return $this;
    }
    
    /**
     * This parameter is optional.
     *
     * A string value indicating how the server should respond to authorization requests.
     * This parameter is specially useful when you are mainly interested in either the overall decision or the permissions granted by the server,
     * instead of a standard OAuth2 response.
     *
     * Possible values are:
     *   decision - Indicates that responses from the server should only represent the overall decision by returning a JSON with the following format:
     *       {
     *           'result': true
     *       }
     *
     *   permission - Indicates that responses from the server should contain any permission granted by the server by returning a JSON with the following format:
     *       [
     *          {
     *              'rsid': 'My Resource'
     *              'scopes': ['view', 'update']
     *          },
     *
     *          ...
     *       ]
     *
     * @param string $mode
     * @return \Cloudcogs\OAuth2\Client\Provider\Keycloak\RequestingPartyTokenRequest
     */
    public function setResponseMode(string $mode = "decision")
    {
        $this->getPost()->set('response_mode', $mode);
        return $this;
    }
}
