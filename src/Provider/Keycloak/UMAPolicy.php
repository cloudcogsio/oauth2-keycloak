<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\InvalidDecisionStrategy;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\InvalidUMAPolicyLogic;

class UMAPolicy
{
    CONST ID = "id";
    CONST NAME = "name";
    CONST DESCRIPTION = "description";
    CONST TYPE = "type";
    CONST SCOPES = "scopes";
    CONST ROLES = "roles";
    CONST GROUPS = "groups";
    CONST CLIENTS = "clients";
    CONST LOGIC = "logic";
    CONST DECISION_STRATEGY = "decisionStrategy";
    CONST OWNER = "owner";
    CONST RESOURCE_ID = "resource_id";
    
    CONST LOGIC_POSITIVE = "POSITIVE";
    CONST LOGIC_NEGATIVE = "NEGATIVE";
    
    CONST DECISION_STRATEGY_UNANIMOUS = "UNANIMOUS";
    CONST DECISION_STRATEGY_AFFIRMATIVE = "AFFIRMATIVE";
    CONST DECISION_STRATEGY_CONSENSUS = "CONSENSUS";
    
    const POLICY_TYPE_UMA = "uma";
    
    private array $data;
    private ?string $resourceId;

    /**
     * @throws InvalidUMAPolicyLogic
     * @throws InvalidDecisionStrategy
     */
    public function __construct(string $resourceId = null, array $data = [])
    {
        $this->resourceId = $resourceId;
        $this->data = $this->validateDataArray($data);
    }
    
    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }
    
    public function getId() : ?string
    {
        return $this->{self::ID};
    }
    
    public function setId(string $id): UMAPolicy
    {
        $this->data[self::ID] = $id;
        return $this;
    }
    
    public function getName() : ?string
    {
        return $this->{self::NAME};
    }
    
    public function setName(string $name): UMAPolicy
    {
        $this->data[self::NAME] = $name;
        return $this;
    }
    
    public function getDescription() : ?string
    {
        return $this->{self::DESCRIPTION};
    }
    
    public function setDescription(string $description): UMAPolicy
    {
        $this->data[self::DESCRIPTION] = $description;
        return $this;
    }
    
    public function getType() : ?string
    {
        return $this->{self::TYPE};
    }
    
    public function setType(string $type = self::POLICY_TYPE_UMA): UMAPolicy
    {
        $this->data[self::TYPE] = $type;
        return $this;
    }
    
    public function getScopes() : array
    {
        return $this->{self::SCOPES};
    }
    
    public function setScopes(array $scopes): UMAPolicy
    {
        $this->data[self::SCOPES] = $scopes;
        return $this;
    }
    
    public function getRoles() : array
    {
        return $this->{self::ROLES};
    }
    
    public function setRoles(array $roles): UMAPolicy
    {
        $this->data[self::ROLES] = $roles;
        return $this;
    }
    
    public function getGroups() : array
    {
        return $this->{self::GROUPS};
    }
    
    public function setGroups(array $groups): UMAPolicy
    {
        $this->data[self::GROUPS] = $groups;
        return $this;
    }
    
    public function getClients() : array
    {
        return $this->{self::CLIENTS};
    }
    
    public function setClients(array $clients): UMAPolicy
    {
        $this->data[self::CLIENTS] = $clients;
        return $this;
    }
    
    public function getLogic() : ?string
    {
        return $this->{self::LOGIC};
    }

    /**
     * @param string $logic
     * @return $this
     * @throws InvalidUMAPolicyLogic
     */
    public function setLogic(string $logic): UMAPolicy
    {
        $this->verifyLogicOption($logic);
        
        $this->data[self::LOGIC] = $logic;
        return $this;
    }
    
    public function getDecisionStrategy() : ?string
    {
        return $this->{self::DECISION_STRATEGY};
    }

    /**
     * @param string $strategy
     * @return $this
     * @throws InvalidDecisionStrategy
     */
    public function setDecisionStrategy(string $strategy): UMAPolicy
    {
        $this->verifyDecisionStrategyOption($strategy);
        
        $this->data[self::DECISION_STRATEGY] = $strategy;
        return $this;
    }
    
    public function getOwner() : ?string
    {
        return $this->{self::OWNER};
    }
    
    public function setOwner(string $owner): UMAPolicy
    {        
        $this->data[self::OWNER] = $owner;
        return $this;
    }
    
    public function getData() : array
    {
        return $this->data;
    }
    
    public function __get($param)
    {
        return (array_key_exists($param, $this->data)) ? $this->data[$param] : null;
    }
    
    public function __toString()
    {
        if ($this->{self::RESOURCE_ID}) unset($this->data[self::RESOURCE_ID]);
        return json_encode($this->data);
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidDecisionStrategy
     * @throws InvalidUMAPolicyLogic
     */
    protected function validateDataArray(array $data) : array
    {
        $self = new \ReflectionClass($this);
        
        if (array_key_exists(self::RESOURCE_ID, $data)) unset($data[self::RESOURCE_ID]);
        $valid = array_flip($self->getConstants());
        
        $verified = array_intersect_key($data, $valid);
        $verified[self::TYPE] = "uma";
        
        if (isset($verified[self::DECISION_STRATEGY]))
        {
            $this->verifyDecisionStrategyOption($verified[self::DECISION_STRATEGY]);
        }
        
        if (isset($verified[self::LOGIC]))
        {
            $this->verifyLogicOption($verified[self::LOGIC]);
        }
        
        return $verified;
    }

    /**
     * @param string $value
     * @return bool
     * @throws InvalidUMAPolicyLogic
     */
    protected function verifyLogicOption(string $value): bool
    {
        if (!in_array($value, [self::LOGIC_NEGATIVE, self::LOGIC_POSITIVE]))
        {
            throw new InvalidUMAPolicyLogic($value);
        }
        
        return true;
    }

    /**
     * @param string $value
     * @return bool
     * @throws InvalidDecisionStrategy
     */
    protected function verifyDecisionStrategyOption(string $value): bool
    {
        if (!in_array($value, [self::DECISION_STRATEGY_AFFIRMATIVE, self::DECISION_STRATEGY_CONSENSUS, self::DECISION_STRATEGY_UNANIMOUS]))
        {
            throw new InvalidDecisionStrategy($value);
        }
        
        return true;
    }
}
