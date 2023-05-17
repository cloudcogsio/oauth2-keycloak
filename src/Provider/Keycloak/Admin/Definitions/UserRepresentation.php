<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources\Users;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_userrepresentation
 */
class UserRepresentation extends AbstractDefinition
{
    const ACCESS = "access";
    const ATTRIBUTES = "attributes";
    const CLIENT_CONSENTS = "clientConsents";
    const CLIENT_ROLES = "clientRoles";
    const CREATED_TIMESTAMP = "createdTimestamp";
    const CREDENTIALS = "credentials";
    const DISABLEABLE_CREDENTIAL_TYPES = "disableableCredentialTypes";
    const EMAIL = "email";
    const EMAIL_VERIFIED = "emailVerified";
    const ENABLED = "enabled";
    const FEDERATED_IDENTITIES = "federatedIdentities";
    const FEDERATION_LINK = "federationLink";
    const FIRST_NAME = "firstName";
    const GROUPS = "groups";
    const ID = "id";
    const LAST_NAME = "lastName";
    const NOT_BEFORE = "notBefore";
    const ORIGIN = "origin";
    const REALM_ROLES = "realmRoles";
    const REQUIRED_ACTIONS = "requiredActions";
    const SELF = "self";
    const SERVICE_ACCOUNT_CLIENT_ID = "serviceAccountClientId";
    const USERNAME = "username";
    
    const TOTP = "totp";
    
    const ACTION_UPDATE_PROFILE = "update_profile";
    const ACTION_UPDATE_PASSWORD = "update_password";
    const ACTION_VERIFY_EMAIL = "verify_email";
    const ACTION_UPDATE_USER_LOCALE = "update_user_locale";
    const ACTION_CONFIGURE_TOTP = "configure_totp";

    protected Users $Users;

    public function __construct(array $data = [], Users $Users)
    {
        $this->Users = $Users;
        parent::__construct($data);
    }

    /**
     * @return UserRepresentation
     * @throws IdentityProviderException
     */
    public function save(): UserRepresentation
    {
        return $this->Users->updateUser($this);
    }

    /**
     * @return bool
     * @throws IdentityProviderException
     */
    public function delete() : bool
    {
        return $this->Users->deleteUser($this);
    }

    /**
     * @return array|null
     */
    public function getAccess() : ?array
    {
        return $this->{self::ACCESS};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAccess(array $value): UserRepresentation
    {
        $this->data[self::ACCESS] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttributes() : ?array
    {
        return $this->{self::ATTRIBUTES};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAttributes(array $value): UserRepresentation
    {
        $this->data[self::ATTRIBUTES] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getClientConsents() : ?array
    {
        return $this->{self::CLIENT_CONSENTS};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setClientConsents(array $value): UserRepresentation
    {
        $this->data[self::CLIENT_CONSENTS] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getClientRoles() : ?array
    {
        return $this->{self::CLIENT_ROLES};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setClientRoles(array $value): UserRepresentation
    {
        $this->data[self::CLIENT_ROLES] = $value;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedTimestamp(): ?int
    {
        return $this->{self::CREATED_TIMESTAMP};
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCreatedTimestamp(int $value): UserRepresentation
    {
        $this->data[self::CREATED_TIMESTAMP] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCredentials() : ?array
    {
        return $this->{self::CREDENTIALS};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setCredentials(array $value): UserRepresentation
    {
        $this->data[self::CREDENTIALS] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getDisableableCredentialTypes() : ?array
    {
        return $this->{self::DISABLEABLE_CREDENTIAL_TYPES};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setDisableableCredentialTypes(array $value): UserRepresentation
    {
        $this->data[self::DISABLEABLE_CREDENTIAL_TYPES] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail() : ?string
    {
        return $this->{self::EMAIL};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setEmail(string $value): UserRepresentation
    {
        $this->data[self::EMAIL] = $value;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEmailVerified() : ?bool
    {
        return $this->{self::EMAIL_VERIFIED};
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setEmailVerified(bool $value): UserRepresentation
    {
        $this->data[self::EMAIL_VERIFIED] = ($value) ? "true" : "false";
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled() : ?bool
    {
        return $this->{self::ENABLED};
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setEnabled(bool $value): UserRepresentation
    {
        $this->data[self::ENABLED] = ($value) ? "true" : "false";
        return $this;
    }

    /**
     * @return array|null
     */
    public function getFederatedIdentities() : ?array
    {
        return $this->{self::FEDERATED_IDENTITIES};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setFederatedIdentities(array $value): UserRepresentation
    {
        $this->data[self::FEDERATED_IDENTITIES] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFederationLink() : ?string
    {
        return $this->{self::FEDERATION_LINK};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFederationLink(string $value): UserRepresentation
    {
        $this->data[self::FEDERATION_LINK] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->{self::FIRST_NAME};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFirstName(string $value): UserRepresentation
    {
        $this->data[self::FIRST_NAME] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getGroups() : ?array
    {
        return $this->{self::GROUPS};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setGroups(array $value): UserRepresentation
    {
        $this->data[self::GROUPS] = $value;
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
     * @param string $value
     * @return $this
     */
    public function setId(string $value): UserRepresentation
    {
        $this->data[self::ID] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->{self::LAST_NAME};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLastName(string $value): UserRepresentation
    {
        $this->data[self::LAST_NAME] = $value;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNotBefore() : ?int
    {
        return $this->{self::NOT_BEFORE};
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setNotBefore(int $value): UserRepresentation
    {
        $this->data[self::NOT_BEFORE] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrigin(): ?string
    {
        return $this->{self::ORIGIN};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setOrigin(string $value): UserRepresentation
    {
        $this->data[self::ORIGIN] = $value;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getRealmRoles() : ?array
    {
        return $this->{self::REALM_ROLES};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setRealmRoles(array $value): UserRepresentation
    {
        $this->data[self::REALM_ROLES] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredActions() : array
    {
        return $this->{self::REQUIRED_ACTIONS};
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setRequiredActions(array $value): UserRepresentation
    {
        foreach ($value as &$action)
        {
            $action = strtoupper($action);
        }
        
        $this->data[self::REQUIRED_ACTIONS] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSelf(): ?string
    {
        return $this->{self::SELF};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSelf(string $value): UserRepresentation
    {
        $this->data[self::SELF] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getServiceAccountClientId(): ?string
    {
        return $this->{self::SERVICE_ACCOUNT_CLIENT_ID};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceAccountClientId(string $value): UserRepresentation
    {
        $this->data[self::SERVICE_ACCOUNT_CLIENT_ID] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->{self::USERNAME};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUsername(string $value): UserRepresentation
    {
        $this->data[self::USERNAME] = $value;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getTotp(): mixed
    {
        return $this->{self::TOTP};
    }
}
