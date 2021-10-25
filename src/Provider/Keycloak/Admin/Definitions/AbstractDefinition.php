<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

abstract class AbstractDefinition
{
    protected $resourceParams;
    
    protected $data;
    
    public function __construct(array $data = [])
    {
        $validated = $this->validateParams($data);
        $this->data = $validated;
    }
    
    protected function validateParams(array $params)
    {
        if (!$this->resourceParams)
        {
            $self = new \ReflectionClass($this);
            $this->resourceParams = array_flip($self->getConstants());
        }
        
        return array_intersect_key($params, $this->resourceParams);
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function __get($param)
    {
        return (array_key_exists($param, $this->data)) ? $this->data[$param] : null;
    }
    
    public function __toString()
    {
        return json_encode($this->data);
    }
    
    public function generateGettersAndSetters()
    {
        $self = new \ReflectionClass($this);
        foreach ($self->getConstants() as $const=>$value)
        {
            ob_start();
            ?>
    public function get<?= ucfirst($value); ?>()
    {
    	return $this->{self::<?= $const; ?>};
    }
    
    public function set<?= ucfirst($value); ?>($value)
    {
    	$this->data[self::<?= $const; ?>] = $value;
    	return $this;
    }
    
<?php 
            $data = ob_get_clean();
            file_put_contents(@array_pop(explode("\\",get_class($this))).".txt", $data, FILE_APPEND);
        }
    }
}
