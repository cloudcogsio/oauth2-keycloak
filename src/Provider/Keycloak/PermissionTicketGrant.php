<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class PermissionTicketGrant
{
    CONST ID = "id";
    CONST OWNER = "owner";
    CONST RESOURCE = "resource";
    CONST SCOPE = "scope";
    CONST GRANTED = "granted";
    CONST REQUESTER = "requester";
    
    private $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $this->validateDataArray($data);
    }
    
    public function getId() : string
    {
        return $this->{self::ID};
    }
    
    public function setId(string $Id)
    {
        $this->data[self::ID] = $Id;
        return $this;
    }
    
    public function getOwner() : string
    {
        return $this->{self::OWNER};
    }
    
    public function setOwner(string $owner)
    {
        $this->data[self::OWNER] = $owner;
        return $this;
    }
    
    public function getResource() : string
    {
        return $this->{self::RESOURCE};
    }
    
    public function setResource(string $resource)
    {
        $this->data[self::RESOURCE] = $resource;
        return $this;
    }
    
    public function getScope() : string
    {
        return $this->{self::SCOPE};
    }
    
    public function setScope(string $scope) 
    {
        $this->data[self::SCOPE] = $scope;
        return $this;
    }
    
    public function getGranted() : bool
    {
        return $this->{self::GRANTED};
    }
    
    public function setGranted(bool $bool)
    {
        $this->data[self::GRANTED] = ($bool) ? "true" : "false";
        return $this;
    }
    
    public function getRequester() : string
    {
        return $this->{self::REQUESTER};
    }
    
    public function setRequester(string $requester)
    {
        $this->data[self::REQUESTER] = $requester;
        return $this;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    protected function validateDataArray(array $data) : array
    {
        $self = new \ReflectionClass($this);
        $valid = array_flip($self->getConstants());
        
        return array_intersect_key($data, $valid);
    }
    
    public function __get($param)
    {
        return (array_key_exists($param, $this->data)) ? $this->data[$param] : null;
    }
    
    public function __toString()
    {
        return json_encode($this->data);
    }
}
