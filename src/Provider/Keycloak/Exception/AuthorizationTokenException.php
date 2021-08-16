<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class AuthorizationTokenException extends \Exception
{
    protected $message = "An unknown exception occurred while decoding the RPT response.";
}
