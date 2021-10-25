<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

class FederatedIdentityRepresentation extends AbstractDefinition
{
    const IDENTITY_PROVIDER = "identityProvider";
    const USER_ID = "userId";
    const USER_NAME = "userName";
    
    public function getIdentityProvider()
    {
        return $this->{self::IDENTITY_PROVIDER};
    }
    
    public function setIdentityProvider(string $identityProvider)
    {
        $this->data[self::IDENTITY_PROVIDER] = $identityProvider;
        return $this;
    }
    
    public function getUserId()
    {
        return $this->{self::USER_ID};
    }
    
    public function setUserId(string $userId)
    {
        $this->data[self::USER_ID] = $userId;
        return $this;
    }
    
    public function getUserName()
    {
        return $this->{self::USER_NAME};
    }
    
    public function setUserName(string $userName)
    {
        $this->data[self::USER_NAME] = $userName;
        return $this;
    }

}
