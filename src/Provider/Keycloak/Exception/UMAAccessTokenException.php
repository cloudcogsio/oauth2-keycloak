<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class UMAAccessTokenException extends \Exception
{
    protected $message = "Invalid UMA Policy Access Token";
}
