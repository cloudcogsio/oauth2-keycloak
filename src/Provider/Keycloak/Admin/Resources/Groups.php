<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\GroupRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiGroupNotFoundException;

class Groups extends AbstractApiResource
{    
    const PARAM_BRIEF_REPRESENTATION = "briefRepresentation";
    const PARAM_FIRST = "first";
    const PARAM_MAX = "max";
    const PARAM_SEARCH = "search";
    
    public function getGroups(array $params = []) : array
    {
        $validated = $this->validateParams($params);
        $groups = [];
        
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."?".http_build_query($validated),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $groupsData = json_decode((string) $HttpResponse->getBody(), true);
            if (is_array($groupsData))
            {
                return $this->recursiveHydrate($groupsData);
            }
            
            return $groups;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function getGroup(string $Id) : GroupRepresentation
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."/".$Id,
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $groupData = json_decode((string) $HttpResponse->getBody(), true);
            if (is_array($groupData))
            {
                $result = [$groupData];
                $result = $this->recursiveHydrate($result);
                return $result[0];
            }
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function recursiveHydrate($groupList)
    {
        foreach ($groupList as $i=>$groupData)
        {
            if (array_key_exists(GroupRepresentation::SUB_GROUPS, $groupData) && count($groupData[GroupRepresentation::SUB_GROUPS]) > 0)
            {
                $groupData[GroupRepresentation::SUB_GROUPS] = $this->recursiveHydrate($groupData[GroupRepresentation::SUB_GROUPS]);
            }
            
            $groupList[$i] = new GroupRepresentation($groupData);
        }
        
        return $groupList;
    }
    
    public function addGroup(GroupRepresentation $Group)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getEndpoint(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], $Group->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            $newGroup = $this->getGroups([Groups::PARAM_BRIEF_REPRESENTATION => false, Groups::PARAM_SEARCH => $Group->getName()]);
            if (is_array($newGroup) && count($newGroup) === 1) return $newGroup[0];
            
            return true;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function updateGroup(GroupRepresentation $Group)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getEndpoint()."/".$Group->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], $Group->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return $this->getGroup($Group->getId());
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function deleteGroup(GroupRepresentation $Group)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getEndpoint()."/".$Group->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            true;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function setOrCreateChildGroup(string $parentGroupId, GroupRepresentation $ChildGroup)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getEndpoint()."/$parentGroupId/children",
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], $ChildGroup->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            $newGroup = $this->getGroups([Groups::PARAM_BRIEF_REPRESENTATION => false, Groups::PARAM_SEARCH => $ChildGroup->getName()]);
            if (is_array($newGroup)) 
            {        
                $createdGroup = $this->recursiveSearchByName($ChildGroup->getName(), $newGroup);
                if ($createdGroup instanceof GroupRepresentation) return $createdGroup;
            }
            
            throw new ApiGroupNotFoundException();
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function recursiveSearchByName(string $name, array $Groups)
    {
        foreach ($Groups as $group)
        {
            if (is_array($group->getSubGroups())) {
                $subGroup = $this->recursiveSearchByName($name, $group->getSubGroups());
                if ($subGroup instanceof GroupRepresentation) return $subGroup;
            }
            
            if ($group->getName() == $name) return $group;
        }
        
        return null;
    }
    
    public function getMembers(GroupRepresentation $Group, array $params = []) : array
    {
        $members = [];
        $validated = $this->validateParams($params);
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."/".$Group->getId()."/members?".http_build_query($validated),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $Users = (new ClientFactory())($this->Keycloak, ClientFactory::RESOURCE_USERS);
            $usersData = json_decode((string) $HttpResponse->getBody(), true);
            foreach ($usersData as $userData)
            {
                $members[] = $Users->hydrate($userData);
            }
            
            return $members;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
}
