<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

/**
 * @see https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_credentialrepresentation
 */
class CredentialRepresentation extends AbstractDefinition
{
    const CREATED_DATE = "createdDate";
    const CREDENTIAL_DATA = "credentialData";
    const ID = "id";
    const PRIORITY = "priority";
    const SECRET_DATA = "secretData";
    const TEMPORARY = "temporary";
    const TYPE = "type";
    const USER_LABEL = "userLabel";
    const VALUE = "value";

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
    public function setCreatedDate(int $createdDate): CredentialRepresentation
    {
        $this->data[self::CREATED_DATE] = $createdDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCredentialData(): ?string
    {
        return $this->{self::CREDENTIAL_DATA};
    }

    /**
     * @param string $credentialData
     * @return $this
     */
    public function setCredentialData(string $credentialData): CredentialRepresentation
    {
        $this->data[self::CREDENTIAL_DATA] = $credentialData;
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
     * @param string $id
     * @return $this
     */
    public function setId(string $id): CredentialRepresentation
    {
        $this->data[self::ID] = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->{self::PRIORITY};
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): CredentialRepresentation
    {
        $this->data[self::PRIORITY] = $priority;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecretData(): ?string
    {
        return $this->{self::SECRET_DATA};
    }

    /**
     * @param string $secretData
     * @return $this
     */
    public function setSecretData(string $secretData): CredentialRepresentation
    {
        $this->data[self::SECRET_DATA] = $secretData;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getTemporary(): ?bool
    {
        return $this->{self::TEMPORARY};
    }

    /**
     * @param bool $temporary
     * @return $this
     */
    public function setTemporary(bool $temporary): CredentialRepresentation
    {
        $this->data[self::TEMPORARY] = $temporary;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->{self::TYPE};
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): CredentialRepresentation
    {
        $this->data[self::TYPE] = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserLabel(): ?string
    {
        return $this->{self::USER_LABEL};
    }

    /**
     * @param string $userLabel
     * @return $this
     */
    public function setUserLabel(string $userLabel): CredentialRepresentation
    {
        $this->data[self::USER_LABEL] = $userLabel;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->{self::VALUE};
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): CredentialRepresentation
    {
        $this->data[self::VALUE] = $value;
        return $this;
    }
}
