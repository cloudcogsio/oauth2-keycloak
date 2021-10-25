<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

class UserConsentRepresentation extends AbstractDefinition
{
    const CLIENT_ID = "clientId";
    const CREATED_DATE = "createdDate";
    const GRANTED_CLIENT_SCOPES = "grantedClientScopes";
    const LAST_UPDATED_DATE = "lastUpdatedDate";
    
    public function getClientId()
    {
        return $this->{self::CLIENT_ID};
    }
    
    public function setClientId(string $clientId)
    {
        $this->data[self::CLIENT_ID] = $clientId;
        return $this;
    }
    
    public function getCreatedDate()
    {
        return $this->{self::CREATED_DATE};
    }
    
    public function setCreatedDate(int $createdDate)
    {
        $this->data[self::CREATED_DATE] = $createdDate;
        return $this;
    }
    
    public function getGrantedClientScopes()
    {
        return $this->{self::GRANTED_CLIENT_SCOPES};
    }
    
    public function setGrantedClientScopes(array $clientScopes)
    {
        $this->data[self::GRANTED_CLIENT_SCOPES] = $clientScopes;
        return $this;
    }
    
    public function getLastUpdatedDate()
    {
        return $this->{self::LAST_UPDATED_DATE};
    }
    
    public function setLastUpdatedDate(int $lastUpdatedDate)
    {
        $this->data[self::LAST_UPDATED_DATE] = $lastUpdatedDate;
        return $this;
    }
}
