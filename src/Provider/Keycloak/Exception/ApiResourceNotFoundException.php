<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class ApiResourceNotFoundException extends \Exception
{
    public function __construct($message = null)
    {
        $this->message = "Unable to load Resource ($message)";
    }
}
