<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

/**
 * @see https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_grouprepresentation
 */
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

    /**
     * @param array $access
     * @return $this
     */
    public function setAccess(array $access): GroupRepresentation
    {
        $this->data[self::ACCESS] = $access;
        return $this;
    }

    /**
     * @return array
     */
    public function getAccess() : array
    {
        return $this->{self::ACCESS};
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): GroupRepresentation
    {
        $this->data[self::ATTRIBUTES] = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->{self::ATTRIBUTES};
    }

    /**
     * @param array $clientRoles
     * @return $this
     */
    public function setClientRoles(array $clientRoles): GroupRepresentation
    {
        $this->data[self::CLIENT_ROLES] = $clientRoles;
        return $this;
    }

    /**
     * @return array
     */
    public function getClientRoles() : array
    {
        return $this->{self::CLIENT_ROLES};
    }

    /**
     * @param string $Id
     * @return $this
     */
    public function setId(string $Id): GroupRepresentation
    {
        $this->data[self::ID] = $Id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->{self::ID};
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): GroupRepresentation
    {
        $this->data[self::NAME] = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->{self::NAME};
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): GroupRepresentation
    {
        $this->data[self::PATH] = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->{self::PATH};
    }

    /**
     * @param array $realmRoles
     * @return $this
     */
    public function setRealmRoles(array $realmRoles): GroupRepresentation
    {
        $this->data[self::REALM_ROLES] = $realmRoles;
        return $this;
    }

    /**
     * @return array
     */
    public function getRealmRoles() : array
    {
        return $this->{self::REALM_ROLES};
    }

    /**
     * @param array $subGroups
     * @return $this
     */
    public function setSubGroups(array $subGroups): GroupRepresentation
    {
        $this->data[self::SUB_GROUPS] = $subGroups;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubGroups() : array
    {
        return $this->{self::SUB_GROUPS};
    }
}
