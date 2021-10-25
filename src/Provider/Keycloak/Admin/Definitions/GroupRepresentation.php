<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

class GroupRepresentation extends AbstractDefinition
{
    const ACCESS = "access";
    const ATTRIBUTES = "attributes";
    const CLIENT_ROLES = "clientRoles";
    const ID = "id";
    const NAME = "name";
    const PATH = "path";
    const REALM_ROLES = "realmRoles";
    const SUB_GROUPS = "subGroups";
    
    public function setAccess(array $access)
    {
        $this->data[self::ACCESS] = $access;
        return $this;
    }
    
    public function getAccess() : array
    {
        return $this->{self::ACCESS};
    }
    
    public function setAttributes(array $attributes)
    {
        $this->data[self::ATTRIBUTES] = $attributes;
        return $this;
    }
    
    public function getAttributes() : array
    {
        return $this->{self::ATTRIBUTES};
    }
    
    public function setClientRoles(array $clientRoles)
    {
        $this->data[self::CLIENT_ROLES] = $clientRoles;
        return $this;
    }
    
    public function getClientRoles() : array
    {
        return $this->{self::CLIENT_ROLES};
    }
    
    public function setId(string $Id)
    {
        $this->data[self::ID] = $Id;
        return $this;
    }
    
    public function getId()
    {
        return $this->{self::ID};
    }
    
    public function setName(string $name)
    {
        $this->data[self::NAME] = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->{self::NAME};
    }
    
    public function setPath(string $path)
    {
        $this->data[self::PATH] = $path;
        return $this;
    }
    
    public function getPath()
    {
        return $this->{self::PATH};
    }
    
    public function setRealmRoles(array $realmRoles)
    {
        $this->data[self::REALM_ROLES] = $realmRoles;
        return $this;
    }
    
    public function getRealmRoles() : array
    {
        return $this->{self::REALM_ROLES};
    }
    
    public function setSubGroups(array $subGroups)
    {
        $this->data[self::SUB_GROUPS] = $subGroups;
        return $this;
    }
    
    public function getSubGroups() : array
    {
        return $this->{self::SUB_GROUPS};
    }
}
