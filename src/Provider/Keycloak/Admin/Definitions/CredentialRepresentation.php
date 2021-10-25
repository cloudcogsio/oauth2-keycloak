<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

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
    
    public function getCreatedDate()
    {
        return $this->{self::CREATED_DATE};
    }
    
    public function setCreatedDate(int $createdDate)
    {
        $this->data[self::CREATED_DATE] = $createdDate;
        return $this;
    }

    public function getCredentialData()
    {
        return $this->{self::CREDENTIAL_DATA};
    }
    
    public function setCredentialData(string $credentialData)
    {
        $this->data[self::CREDENTIAL_DATA] = $credentialData;
        return $this;
    }    

    public function getId()
    {
        return $this->{self::ID};
    }
    
    public function setId(string $id)
    {
        $this->data[self::ID] = $id;
        return $this;
    } 

    public function getPriority()
    {
        return $this->{self::PRIORITY};
    }
    
    public function setPriority(int $priority)
    {
        $this->data[self::PRIORITY] = $priority;
        return $this;
    }

    public function getSecretData()
    {
        return $this->{self::SECRET_DATA};
    }
    
    public function setSecretData(string $secretData)
    {
        $this->data[self::SECRET_DATA] = $secretData;
        return $this;
    }

    public function getTemporary()
    {
        return $this->{self::TEMPORARY};
    }
    
    public function setTemporary(bool $temporary)
    {
        $this->data[self::TEMPORARY] = $temporary;
        return $this;
    }

    public function getType()
    {
        return $this->{self::TYPE};
    }
    
    public function setType(string $type)
    {
        $this->data[self::TYPE] = $type;
        return $this;
    }

    public function getUserLabel()
    {
        return $this->{self::USER_LABEL};
    }
    
    public function setUserLabel(string $userLabel)
    {
        $this->data[self::USER_LABEL] = $userLabel;
        return $this;
    }

    public function getValue()
    {
        return $this->{self::VALUE};
    }
    
    public function setValue(string $value)
    {
        $this->data[self::VALUE] = $value;
        return $this;
    }
    
        
}
