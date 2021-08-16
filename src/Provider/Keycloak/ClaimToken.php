<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

class ClaimToken
{
    const FORMAT = "urn:ietf:params:oauth:token-type:jwt";
    
    private $data;
    
    public function __construct(array $claims = [])
    {
        $this->data = $claims;
    }
    
    public function addClaim(string $claim, $value)
    {
        $this->data[$claim] = $value;
        return $this;
    }
    
    public function __toString()
    {
        $json = json_encode((object) $this->data);
        
        return base64_encode($json);
    }
}
