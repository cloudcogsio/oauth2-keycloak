<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions;

abstract class AbstractDefinition
{
    protected array $resourceParams;

    protected array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $validated = $this->validateParams($data);
        $this->data = $validated;
    }

    /**
     * @param array $params
     * @return array
     */
    protected final function validateParams(array $params): array
    {
        if (!isset($this->resourceParams)) {
            $self = new \ReflectionClass($this);
            $this->resourceParams = array_flip($self->getConstants());
        }

        return array_intersect_key($params, $this->resourceParams);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $param
     * @return mixed|null
     */
    public function __get($param)
    {
        return (array_key_exists($param, $this->data)) ? $this->data[$param] : null;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->data);
    }
}
