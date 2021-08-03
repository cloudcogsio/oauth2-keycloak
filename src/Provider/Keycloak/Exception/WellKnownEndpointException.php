<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class WellKnownEndpointException extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "OpenID Connect well-known endpoint error. [$message]";
        $this->code = $code;
    }
}
