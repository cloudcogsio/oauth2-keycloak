<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class RequiredOptionMissingException extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Required option missing [$message]";
    }
}

