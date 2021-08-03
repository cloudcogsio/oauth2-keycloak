<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class TokenIntrospectionException extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Token introspection endpoint error. [$message]";
        $this->code = $code;
    }
}
