<?php

namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class ResourcePermission
{
    const SCOPE = "scope";
    const RESOURCE_SCOPES = "resource_scopes";
    const RESOURCE_ID = "rsid";
    const RESOURCE_NAME = "rsname";

    protected array $permission;

    public function __construct(array $permission)
    {
        $this->permission = $permission;
    }

    public function getResourceName() : string
    {
        return $this->permission[self::RESOURCE_NAME];
    }

    public function getResourceId() : string
    {
        return $this->permission[self::RESOURCE_ID];
    }

    public function getScopes() : array
    {
        return $this->permission[self::SCOPE];
    }

    public function getResourceScopes() : ?array
    {
        return $this->permission[self::RESOURCE_SCOPES];
    }

    public function hasScope(string $scope) : bool
    {
        return in_array($scope, $this->getResourceScopes());
    }
}
