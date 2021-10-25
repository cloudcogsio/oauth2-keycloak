<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

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
    
    public function getAccess() : array
    {
        return $this->{self::ACCESS};
    }
    
    public function setAccess(array $value)
    {
        $this->data[self::ACCESS] = $value;
        return $this;
    }
    
    public function getAttributes() : array
    {
        return $this->{self::ATTRIBUTES};
    }
    
    public function setAttributes(array $value)
    {
        $this->data[self::ATTRIBUTES] = $value;
        return $this;
    }
    
    public function getClientConsents() : array
    {
        return $this->{self::CLIENT_CONSENTS};
    }
    
    public function setClientConsents(array $value)
    {
        $this->data[self::CLIENT_CONSENTS] = $value;
        return $this;
    }
    
    public function getClientRoles() : array
    {
        return $this->{self::CLIENT_ROLES};
    }
    
    public function setClientRoles(array $value)
    {
        $this->data[self::CLIENT_ROLES] = $value;
        return $this;
    }
    
    public function getCreatedTimestamp()
    {
        return $this->{self::CREATED_TIMESTAMP};
    }
    
    public function setCreatedTimestamp(int $value)
    {
        $this->data[self::CREATED_TIMESTAMP] = $value;
        return $this;
    }
    
    public function getCredentials() : array
    {
        return $this->{self::CREDENTIALS};
    }
    
    public function setCredentials(array $value)
    {
        $this->data[self::CREDENTIALS] = $value;
        return $this;
    }
    
    public function getDisableableCredentialTypes() : array
    {
        return $this->{self::DISABLEABLE_CREDENTIAL_TYPES};
    }
    
    public function setDisableableCredentialTypes(array $value)
    {
        $this->data[self::DISABLEABLE_CREDENTIAL_TYPES] = $value;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->{self::EMAIL};
    }
    
    public function setEmail(string $value)
    {
        $this->data[self::EMAIL] = $value;
        return $this;
    }
    
    public function getEmailVerified() : bool
    {
        return $this->{self::EMAIL_VERIFIED};
    }
    
    public function setEmailVerified(bool $value)
    {
        $this->data[self::EMAIL_VERIFIED] = ($value) ? "true" : "false";
        return $this;
    }
    
    public function getEnabled() : bool
    {
        return $this->{self::ENABLED};
    }
    
    public function setEnabled(bool $value)
    {
        $this->data[self::ENABLED] = ($value) ? "true" : "false";
        return $this;
    }
    
    public function getFederatedIdentities() : array
    {
        return $this->{self::FEDERATED_IDENTITIES};
    }
    
    public function setFederatedIdentities(array $value)
    {
        $this->data[self::FEDERATED_IDENTITIES] = $value;
        return $this;
    }
    
    public function getFederationLink()
    {
        return $this->{self::FEDERATION_LINK};
    }
    
    public function setFederationLink($value)
    {
        $this->data[self::FEDERATION_LINK] = $value;
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->{self::FIRST_NAME};
    }
    
    public function setFirstName($value)
    {
        $this->data[self::FIRST_NAME] = $value;
        return $this;
    }
    
    public function getGroups() : array
    {
        return $this->{self::GROUPS};
    }
    
    public function setGroups(array $value)
    {
        $this->data[self::GROUPS] = $value;
        return $this;
    }
    
    public function getId()
    {
        return $this->{self::ID};
    }
    
    public function setId($value)
    {
        $this->data[self::ID] = $value;
        return $this;
    }
    
    public function getLastName()
    {
        return $this->{self::LAST_NAME};
    }
    
    public function setLastName($value)
    {
        $this->data[self::LAST_NAME] = $value;
        return $this;
    }
    
    public function getNotBefore()
    {
        return $this->{self::NOT_BEFORE};
    }
    
    public function setNotBefore(int $value)
    {
        $this->data[self::NOT_BEFORE] = $value;
        return $this;
    }
    
    public function getOrigin()
    {
        return $this->{self::ORIGIN};
    }
    
    public function setOrigin($value)
    {
        $this->data[self::ORIGIN] = $value;
        return $this;
    }
    
    public function getRealmRoles() : array
    {
        return $this->{self::REALM_ROLES};
    }
    
    public function setRealmRoles(array $value)
    {
        $this->data[self::REALM_ROLES] = $value;
        return $this;
    }
    
    public function getRequiredActions() : array
    {
        return $this->{self::REQUIRED_ACTIONS};
    }
    
    public function setRequiredActions(array $value)
    {
        foreach ($value as &$action)
        {
            $action = strtoupper($action);
        }
        
        $this->data[self::REQUIRED_ACTIONS] = $value;
        return $this;
    }
    
    public function getSelf()
    {
        return $this->{self::SELF};
    }
    
    public function setSelf($value)
    {
        $this->data[self::SELF] = $value;
        return $this;
    }
    
    public function getServiceAccountClientId()
    {
        return $this->{self::SERVICE_ACCOUNT_CLIENT_ID};
    }
    
    public function setServiceAccountClientId($value)
    {
        $this->data[self::SERVICE_ACCOUNT_CLIENT_ID] = $value;
        return $this;
    }
    
    public function getUsername()
    {
        return $this->{self::USERNAME};
    }
    
    public function setUsername($value)
    {
        $this->data[self::USERNAME] = $value;
        return $this;
    }
    
    public function getTotp()
    {
        return $this->{self::TOTP};
    }
}
