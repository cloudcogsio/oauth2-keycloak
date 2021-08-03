<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache;

/**
 * Simple filesystem cache for the JWK
 */
class File implements PublicKeyCacheInterface
{
    const FILENAME_EXT = '.keycloak-jwk';
    protected $filename;
    
    public function __construct($filename)
    {
        $this->filename = $filename.self::FILENAME_EXT;
    }
    
    /**
     * {@inheritDoc}
     * @see \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface::save()
     */
    public function save($JWK, array $options = [])
    {
        return file_put_contents($this->filename, serialize($JWK));
    }

    /**
     * {@inheritDoc}
     * @see \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface::load()
     */
    public function load(array $options = [])
    {
        if (file_exists($this->filename))
        {
            return unserialize(file_get_contents($this->filename));
        }
        
        return false;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache\PublicKeyCacheInterface::clear()
     */
    public function clear(array $options = [])
    {
        if (file_exists($this->filename))
        {
            return unlink($this->filename);
        }
        
        return false;
    }
}

