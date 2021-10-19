<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class InvalidDecisionStrategy extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Invalid Descision Strategey [$message]";
    }
}
