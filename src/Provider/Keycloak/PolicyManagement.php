<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

class PolicyManagement extends AbstractAuthorizationServices
{
    const PARAM_NAME = "name";
    const PARAM_RESOURCE = "resource";
    const PARAM_SCOPE = "scope";
    
    private AccessTokenInterface $UMAPolicyAccessToken;

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getPolicies(array $params = []) : array
    {
        $params = $this->validateDataArray($params);
        $list = [];
        
        $PAT = $this->getUMAPolicyAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getPolicyEndpoint().((!empty($params)) ? "?".http_build_query($params) : ""),
            [
                "Authorization"=>"Bearer ".$PAT
            ]);
                
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $policies = json_decode((string) $HttpResponse->getBody());
            if (is_array($policies))
            {
                foreach ($policies as $policy)
                {
                    $list[] = new UMAPolicy(null, (array) $policy);
                }
            }
            
            return $list;
        }
        elseif($HttpResponse->getStatusCode() == "204") {
            return [];
        } 
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param UMAPolicy $UMAPolicy
     * @return bool
     * @throws Exception
     */
    public function deletePolicy(UMAPolicy $UMAPolicy) : bool
    {
        $PAT = $this->getUMAPolicyAccessToken()->getToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getPolicyEndpoint()."/".$UMAPolicy->getId(),
            [
                "Authorization"=>"Bearer ".$PAT,
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
     * @param UMAPolicy $UMAPolicy
     * @return UMAPolicy
     * @throws Exception
     */
    public function createPolicy(UMAPolicy $UMAPolicy) : UMAPolicy
    {
        $PAT = $this->getUMAPolicyAccessToken()->getToken();
        $resourceId = $UMAPolicy->getResourceId();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getPolicyEndpoint()."/".$resourceId,
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ], $UMAPolicy->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $policyData = json_decode((string) $HttpResponse->getBody(), true);
            return new UMAPolicy($resourceId, $policyData);
        }
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param UMAPolicy $UMAPolicy
     * @return bool
     * @throws Exception
     */
    public function updatePolicy(UMAPolicy $UMAPolicy) : bool
    {
        $PAT = $this->getUMAPolicyAccessToken()->getToken();
        $policyId = $UMAPolicy->getId();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getPolicyEndpoint()."/".$policyId,
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ], $UMAPolicy->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            return true;
        }
        else {
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param string|null $Token
     * @return AccessTokenInterface
     * @throws IdentityProviderException
     */
    public function getUMAPolicyAccessToken(string $Token = null) : AccessTokenInterface
    {
        if ($Token)
        {
            $this->UMAPolicyAccessToken = $this->Keycloak->tokenExchange($Token);
        }
        
        return $this->UMAPolicyAccessToken;
    }
    
    public function setUMAPolicyAccessToken(AccessTokenInterface $UMAPolicyAccessToken): PolicyManagement
    {
        $this->UMAPolicyAccessToken = $UMAPolicyAccessToken;
        return $this;
    }
    
    protected function validateDataArray(array $data) : array
    {
        $self = new \ReflectionClass($this);
        $valid = array_flip($self->getConstants());
        
        return array_intersect_key($data, $valid);
    }
}
