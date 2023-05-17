<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Grants;

use League\OAuth2\Client\Grant\AbstractGrant;

class TokenExchange extends AbstractGrant
{
    const REQUESTED_TOKEN_TYPE_ACCESS = "urn:ietf:params:oauth:token-type:access_token";
    const REQUESTED_TOKEN_TYPE_REFRESH = "urn:ietf:params:oauth:token-type:refresh_token";
    
    private string $grant_type = "urn:ietf:params:oauth:grant-type:token-exchange";
    
    /**
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Grant\AbstractGrant::getName()
     */
    protected function getName(): string
    {
        return $this->grant_type;
    }
    
    /**
     * {@inheritDoc}
     * @see \League\OAuth2\Client\Grant\AbstractGrant::getRequiredRequestParameters()
     */
    protected function getRequiredRequestParameters(): array
    {
        return [
            'grant_type',
            'audience',
            'subject_token',
            'requested_token_type'
        ];
    }
}
