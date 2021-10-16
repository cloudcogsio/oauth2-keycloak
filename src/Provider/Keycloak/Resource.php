<?php
/**
 * Copyright 2021, Cloudcogs.io
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class Resource
{    
    const NAME = "name";
    const TYPE = "type";
    const ICON_URI = "icon_uri";
    const RESOURCE_SCOPES = "resource_scopes";
    const OWNER = "owner";
    const OWNER_MANAGED_ACCESS = "ownerManagedAccess";
    const URIS = "uris";
    const SCOPES = "scopes";
    const ID = "_id";
    
    private $config = [
        self::NAME => null,
        self::TYPE => null,
        self::ICON_URI => null,
        self::RESOURCE_SCOPES => null,
        self::OWNER => null,
        self::OWNER_MANAGED_ACCESS => null,
        self::URIS => null,
        self::SCOPES => null,
        self::ID => null
    ];
    
    public function __construct(array $config = [])
    {
        $this->config = array_intersect_key($config, $this->config);
    }
    
    public function setName(string $name)
    {
        $this->config[self::NAME] = $name;
        return $this;
    }
    
    public function getName()
    {
        return @$this->config[self::NAME];
    }
    
    public function setType(string $type)
    {
        $this->config[self::TYPE] = $type;
        return $this;
    }
    
    public function getType()
    {
        return @$this->config[self::TYPE];
    }
    
    public function setIconUri(string $icon_uri)
    {
        $this->config[self::ICON_URI] = $icon_uri;
        return $this;
    }
    
    public function getIconUri()
    {
        return @$this->config[self::ICON_URI];
    }
    
    public function setResourceScopes(array $resourceScopes)
    {
        if(is_array(@$this->config[self::RESOURCE_SCOPES]))
        {
            $this->config[self::RESOURCE_SCOPES] = array_merge($this->config[self::RESOURCE_SCOPES], $resourceScopes);
        } 
        else {
            $this->config[self::RESOURCE_SCOPES] = $resourceScopes;
        }
        
        return $this;
    }
    
    public function getResourceScopes()
    {
        $scopes = [];
        if(is_array(@$this->config[self::RESOURCE_SCOPES]))
        {
            foreach ($this->config[self::RESOURCE_SCOPES] as $scope)
            {
                if (is_string($scope))
                {
                    $scopes[] = (object) ["name" => $scope];
                }
                else {
                    $scopes[] = $scope;
                }
            }
        }
        
        return $scopes;
    }
    
    public function setOwner(string $owner)
    {
        $this->config[self::OWNER] = $owner;
        return $this;
    }
    
    public function getOwner()
    {
        return @$this->config[self::OWNER];
    }
    
    public function setOwnerManagedAccess(bool $value)
    {
        $this->config[self::OWNER_MANAGED_ACCESS] = $value;
        return $this;
    }
    
    public function getOwnerManagedAccess()
    {
        return @$this->config[self::NAME];
    }
    
    public function setUris(array $uris)
    {
        if(is_array(@$this->config[self::URIS]))
        {
            $this->config[self::URIS] = array_merge($this->config[self::URIS], $uris);
        }
        else {
            $this->config[self::URIS] = $uris;
        }
        
        return $this;
    }
    
    public function getUris()
    {
        return @$this->config[self::URIS];
    }
    
    public function getId()
    {
        return @$this->config[self::ID];
    }
    
    public function getScopes()
    {
        return @$this->config[self::SCOPES];
    }
    
    public function __toString()
    {
        return json_encode($this->config);
    }
}
