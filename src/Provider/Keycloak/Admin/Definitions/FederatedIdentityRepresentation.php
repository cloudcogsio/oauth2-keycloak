<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

/**
 * @see https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_federatedidentityrepresentation
 */
class FederatedIdentityRepresentation extends AbstractDefinition
{
    const IDENTITY_PROVIDER = "identityProvider";
    const USER_ID = "userId";
    const USER_NAME = "userName";

    /**
     * @return string|null
     */
    public function getIdentityProvider(): ?string
    {
        return $this->{self::IDENTITY_PROVIDER};
    }

    /**
     * @param string $identityProvider
     * @return $this
     */
    public function setIdentityProvider(string $identityProvider): FederatedIdentityRepresentation
    {
        $this->data[self::IDENTITY_PROVIDER] = $identityProvider;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->{self::USER_ID};
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function setUserId(string $userId): FederatedIdentityRepresentation
    {
        $this->data[self::USER_ID] = $userId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->{self::USER_NAME};
    }

    /**
     * @param string $userName
     * @return $this
     */
    public function setUserName(string $userName): FederatedIdentityRepresentation
    {
        $this->data[self::USER_NAME] = $userName;
        return $this;
    }
}
