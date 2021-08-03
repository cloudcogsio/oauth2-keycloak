<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class InvalidConfigFileException extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Configuration file is invalid or missing [$message]";
    }
}