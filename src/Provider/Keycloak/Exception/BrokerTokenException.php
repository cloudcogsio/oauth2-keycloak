<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception;

class BrokerTokenException extends \Exception
{
    protected $message = "An unknown exception occurred while retrieving the broker token.";
}
