<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\GroupRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiGroupNotFoundException;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiResourceNotFoundException;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * @see https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_groups_resource
 */
class Groups extends AbstractApiResource
{    
    const PARAM_BRIEF_REPRESENTATION = "briefRepresentation";
    const PARAM_FIRST = "first";
    const PARAM_MAX = "max";
    const PARAM_SEARCH = "search";

    /**
     * Get group hierarchy.
     *
     * @param array $params
     * @return array|null
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function getGroups(array $params = []) : ?array
    {
        $groups = [];
        $HttpResponse = $this->getResourceData($params);
        
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
            throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }

    /**
     * @param string $Id
     * @return GroupRepresentation|null
     * @throws ApiGroupNotFoundException ;
     * @throws IdentityProviderException
     */
    public function getGroup(string $Id) : ?GroupRepresentation
    {
        $HttpResponse = $this->getResource($Id);
        $groupData = json_decode((string) $HttpResponse->getBody(), true);
        if (is_array($groupData))
        {
            $result = [$groupData];
            $result = $this->recursiveHydrate($result);
            return $result[0];
        }

        throw new ApiGroupNotFoundException($Id);
    }

    /**
     * @param array $groupList
     * @return array
     */
    public function recursiveHydrate(array $groupList): array
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

    /**
     * @param GroupRepresentation $Group
     * @return GroupRepresentation|null
     * @throws IdentityProviderException
     */
    public function addGroup(GroupRepresentation $Group) : ?GroupRepresentation
    {
        $this->addResource($Group->__toString());
        
        $newGroup = $this->getGroups([Groups::PARAM_BRIEF_REPRESENTATION => false, Groups::PARAM_SEARCH => $Group->getName()]);
        if (count($newGroup) === 1)
            return $newGroup[0];

        return null;
    }

    /**
     * @param GroupRepresentation $Group
     * @return GroupRepresentation
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function updateGroup(GroupRepresentation $Group): GroupRepresentation
    {
        $this->updateResource($Group->getId(), $Group->__toString());
        return $this->getGroup($Group->getId());
    }

    /**
     * @param GroupRepresentation $Group
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function deleteGroup(GroupRepresentation $Group): bool
    {
        return $this->deleteResource($Group->getId());
    }

    /**
     * @param string $parentGroupId
     * @param GroupRepresentation $ChildGroup
     * @return GroupRepresentation
     * @throws ApiGroupNotFoundException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function setOrCreateChildGroup(string $parentGroupId, GroupRepresentation $ChildGroup): GroupRepresentation
    {
        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "POST",
                $this->getEndpoint()."/$parentGroupId/children",
                $this->getHttpRequestHeaders(),
                $ChildGroup->__toString()
            );
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            $newGroup = $this->getGroups([Groups::PARAM_BRIEF_REPRESENTATION => false, Groups::PARAM_SEARCH => $ChildGroup->getName()]);
            $createdGroup = $this->recursiveSearchByName($ChildGroup->getName(), $newGroup);
            if ($createdGroup instanceof GroupRepresentation)
                return $createdGroup;
            
            throw new ApiGroupNotFoundException();
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param string $name
     * @param array $Groups
     * @return GroupRepresentation|null
     */
    public function recursiveSearchByName(string $name, array $Groups): ?GroupRepresentation
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

    /**
     * @param GroupRepresentation $Group
     * @param array $params
     * @return array
     * @throws ApiResourceNotFoundException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function getMembers(GroupRepresentation $Group, array $params = []) : array
    {
        $members = [];
        $validated = $this->validateParams($params);

        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "GET",
                $this->getEndpoint()."/".$Group->getId()."/members?".http_build_query($validated),
                $this->getHttpRequestHeaders()
            );
        
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

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }
}
