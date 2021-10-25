<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class ApiResourceException extends \Exception
{
    protected $message = "Invalid Resource Specified";
}
