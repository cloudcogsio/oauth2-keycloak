<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\PublicKeyCache;

interface PublicKeyCacheInterface
{
    /**
     * Save the JWK
     * 
     * @param string | array $JWK
     */
    public function save($JWK, array $options = []);
    
    /**
     * Retrieve the JWK
     * 
     * @return false if not exists or the JWK
     */
    public function load(array $options = []);
    
    /**
     * Clear one or more saved JWKs
     */
    public function clear(array $options = []);
}
