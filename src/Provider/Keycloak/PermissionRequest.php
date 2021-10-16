<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class PermissionRequest
{
    const RESOURCE_ID = "resource_id";
    const RESOURCE_SCOPES = "resource_scopes";
    const CLAIMS = "claims";
    
    private $requestData;
    
    public function __construct(string $resourceId, array $scopes = [], array $claims = [])
    {
        $this->requestData[self::RESOURCE_ID] = $resourceId;
        $this->requestData[self::RESOURCE_SCOPES] = $scopes;
        $this->requestData[self::CLAIMS] = [];
        
        if (!empty($claims))
        {
            foreach ($claims as $claim => $value)
            {
                $this->addClaim($claim, $value);
            }
        }
    }
    
    public function addScope(string $scope)
    {
        if (!in_array($scope, $this->requestData[self::RESOURCE_SCOPES]))
            $this->requestData[self::RESOURCE_SCOPES][] = $scope;
        
        return $this;
    }
    
    public function addClaim(string $claim, $value)
    {
        $this->requestData[self::CLAIMS][$claim] = $value;
        return $this;
    }
    
    public function getRequestData()
    {
        return $this->requestData;
    }
    
    public function __toString()
    {
        return json_encode($this->requestData);
    }
}
