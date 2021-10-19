<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class InvalidUMAPolicyLogic extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->message = "Invalid UMA Policy Logic [$message]";
    }
}
