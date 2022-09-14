<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class PermissionManagement extends AbstractAuthorizationServices
{
    const PARAM_SCOPE_ID = "scopeId";
    const PARAM_RESOURCE_ID = "resourceId";
    const PARAM_OWNER = "owner";
    const PARAM_REQUESTER = "requester";
    const PARAM_GRANTED = "granted";
    const PARAM_RETURN_NAMES = "returnNames";
    const PARAM_FIRST = "first";
    const PARAM_MAX = "max";
    
    private array $permissionList;
    
    /**
     * Create a permission ticket that represents a permission request.
     * 
     * This ticket must then be submitted to the token endpoint to determine if access is allowed 
     * or to request the permission that is represented in the ticket (UMA).
     * 
     * Permission requests must first be added by calling $this->addPermissionRequest BEFORE calling this method.
     * 
     * @see https://www.keycloak.org/docs/latest/authorization_services/#creating-permission-ticket
     * 
     * @throws Exception
     * @return string
     */
    public function createPermissionTicket() : string
    {
        if (isset($this->permissionList))
        {
            $PAT = $this->getProtectionAPIToken();
            
            $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getPermissionEndpoint(),
                [
                    "Authorization"=>"Bearer ".$PAT,
                    "Content-Type"=>"application/json"
                ], json_encode($this->permissionList));
            
            $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
            
            $this->permissionList = [];
            
            if ($HttpResponse->getStatusCode() == "201")
            {
                $resourceData = (object) json_decode((string) $HttpResponse->getBody());
                return $resourceData->ticket;
            }
            else {
                throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
            }
        }
        else {
            throw new Exception("No permission requests found");
        }
    }
    
    /**
     * Add a permission request to the list of pending requests before submitting with $this->createPermissionTicket
     * 
     * @param PermissionRequest $PermissionRequest
     * @return PermissionManagement
     */
    public function addPermissionRequest(PermissionRequest $PermissionRequest): PermissionManagement
    {
        if (!isset($this->permissionList)) $this->permissionList = [];
        
        $this->permissionList[] = $PermissionRequest->getRequestData();
        
        return $this;
    }
    
    /**
     * Retrieve a list of pending permission tickets in KC.
     * Use params to filter the request.
     * 
     * @see https://www.keycloak.org/docs/latest/authorization_services/#getting-permission-tickets
     *  
     * @param array $params
     * @throws Exception
     * @return array
     */
    public function getPermissionTicketGrants(array $params = []) : array
    {        
        $params = $this->validateDataArray($params);
        $list = [];
        
        $PAT = $this->getProtectionAPIToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getPermissionEndpoint()."/ticket".((!empty($params)) ? "?".http_build_query($params) : ""),
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
                
        if ($HttpResponse->getStatusCode() == "200")
        {
            $grants = json_decode((string) $HttpResponse->getBody());
            if (is_array($grants))
            {
                foreach ($grants as $grant)
                {
                    $list[] = new PermissionTicketGrant((array) $grant);
                }
            }
            
            return $list;
        }
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param PermissionTicketGrant $PermissionTicketGrant
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function updatePermissionTicketGrant(PermissionTicketGrant $PermissionTicketGrant) : bool
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getPermissionEndpoint()."/ticket",
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ], $PermissionTicketGrant->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param PermissionTicketGrant $PermissionTicketGrant
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function deletePermissionTicketGrant(PermissionTicketGrant $PermissionTicketGrant) : bool
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getPermissionEndpoint()."/ticket/".$PermissionTicketGrant->getId(),
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function validateDataArray(array $data) : array
    {
        $self = new \ReflectionClass($this);
        $valid = array_flip($self->getConstants());
        
        return array_intersect_key($data, $valid);
    }
}
