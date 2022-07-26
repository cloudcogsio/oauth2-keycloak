<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

/**
 * https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_userconsentrepresentation
 */
class UserConsentRepresentation extends AbstractDefinition
{
    const CLIENT_ID = "clientId";
    const CREATED_DATE = "createdDate";
    const GRANTED_CLIENT_SCOPES = "grantedClientScopes";
    const LAST_UPDATED_DATE = "lastUpdatedDate";

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->{self::CLIENT_ID};
    }

    /**
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId): UserConsentRepresentation
    {
        $this->data[self::CLIENT_ID] = $clientId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedDate(): ?int
    {
        return $this->{self::CREATED_DATE};
    }

    /**
     * @param int $createdDate
     * @return $this
     */
    public function setCreatedDate(int $createdDate): UserConsentRepresentation
    {
        $this->data[self::CREATED_DATE] = $createdDate;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getGrantedClientScopes(): ?array
    {
        return $this->{self::GRANTED_CLIENT_SCOPES};
    }

    /**
     * @param array $clientScopes
     * @return $this
     */
    public function setGrantedClientScopes(array $clientScopes): UserConsentRepresentation
    {
        $this->data[self::GRANTED_CLIENT_SCOPES] = $clientScopes;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastUpdatedDate(): ?int
    {
        return $this->{self::LAST_UPDATED_DATE};
    }

    /**
     * @param int $lastUpdatedDate
     * @return $this
     */
    public function setLastUpdatedDate(int $lastUpdatedDate): UserConsentRepresentation
    {
        $this->data[self::LAST_UPDATED_DATE] = $lastUpdatedDate;
        return $this;
    }
}
