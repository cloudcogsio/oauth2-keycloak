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

/**
 * Enables Keycloak resource servers to remotely manage their resources
 * 
 * @see https://www.keycloak.org/docs/latest/authorization_services/#_service_protection_resources_api
 */
class ResourceManagement extends AbstractAuthorizationServices
{
    const QUERY_NAME = "name";
    const QUERY_URI = "uri";
    const QUERY_OWNER = "owner";
    const QUERY_TYPE = "type";
    const QUERY_SCOPE = "scope";
    const PARAM_NAME_EXACT = "exactName";
    const PARAM_FIRST = "first";
    const PARAM_MAX = "max";
    
    public function getResource(string $resourceId) : Resource
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getResourceRegistrationEndpoint()."/".$resourceId,
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $resourceData = (object) json_decode((string) $HttpResponse->getBody());
            return new Resource((array) $resourceData);
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function createResource(Resource $Resource) : Resource
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getResourceRegistrationEndpoint(),
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ], $Resource->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            $resourceData = (object) json_decode((string) $HttpResponse->getBody());
            return new Resource((array) $resourceData);
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function updateResource(string $resourceId, Resource $Resource) : bool
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getResourceRegistrationEndpoint()."/".$resourceId,
            [
                "Authorization"=>"Bearer ".$PAT,
                "Content-Type"=>"application/json"
            ], $Resource->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function deleteResource(string $resourceId) : bool
    {
        $PAT = $this->getProtectionAPIToken();
        
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getResourceRegistrationEndpoint()."/".$resourceId,
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
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function listResources(array $query_filters, array $query_params = []) : array
    {
        $valid_filters = [
            self::QUERY_NAME => null,
            self::QUERY_OWNER => null,
            self::QUERY_SCOPE => null,
            self::QUERY_TYPE => null,
            self::QUERY_URI => null
        ];
        
        $valid_params = [
            self::PARAM_FIRST => null,
            self::PARAM_MAX => null,
            self::PARAM_NAME_EXACT => null,
        ];
        
        $query_filters = array_intersect_key($query_filters, $valid_filters);
        
        if (!empty($query_params))
        {
            $query_params = array_intersect_key($query_params, $valid_params);
        }
        
        $query = array_merge($query_filters, $query_params);
        
        if (!empty($query))
        {
            $PAT = $this->getProtectionAPIToken();
            $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getResourceRegistrationEndpoint()."?".http_build_query($query),
                [
                    "Authorization"=>"Bearer ".$PAT,
                    "Content-Type"=>"application/json"
                ]);
            
            $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
            
            if ($HttpResponse->getStatusCode() == "200")
            {
                return json_decode((string) $HttpResponse->getBody());
            }
            else {
                throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
            }
        }
        else {
            throw new \Exception("No valid query filters detected");
        }
    }
}
