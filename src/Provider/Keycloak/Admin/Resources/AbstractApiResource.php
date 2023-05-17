<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApiResource
{
    /** @var Keycloak **/
    protected Keycloak $Keycloak;
    
    protected AccessToken $ClientCredentialsToken;
    protected string $endpoint;
    protected array $resourceParams;

    /**
     * @param Keycloak $Keycloak
     * @param string $endpoint
     */
    public function __construct(Keycloak $Keycloak, string $endpoint)
    {
        $this->Keycloak = $Keycloak;
        
        $this->setEndpoint($endpoint);
    }

    /**
     * @return AccessToken
     * @throws IdentityProviderException
     */
    protected final function getAccessToken() : AccessToken
    {
        if(!isset($this->ClientCredentialsToken))
        {
            $this->ClientCredentialsToken = $this->Keycloak->getAccessToken("client_credentials");
        }
        
        return $this->ClientCredentialsToken;
    }

    /**
     * @param string $resourceEndpoint
     * @return $this
     */
    protected final function setEndpoint(string $resourceEndpoint): AbstractApiResource
    {
        $this->endpoint = $this->Keycloak->getAdminApiBaseUrl().$resourceEndpoint;
        return $this;
    }

    /**
     * @return string
     */
    protected final function getEndpoint() : string
    {
        return $this->endpoint;
    }

    /**
     * @param array $params
     * @return array
     */
    protected final function validateParams(array $params): array
    {
        if (!isset($this->resourceParams))
        {
            $self = new \ReflectionClass($this);
            $this->resourceParams = array_flip($self->getConstants());
        }
        
        return array_intersect_key($params, $this->resourceParams);
    }

    /**
     * @param array $params
     * @return ResponseInterface
     * @throws IdentityProviderException
     */
    protected function getResourceData(array $params = []): ResponseInterface
    {
        $validated = $this->validateParams($params);

        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "GET",
                $this->getEndpoint()."?".http_build_query($validated),
                $this->getHttpRequestHeaders()
            );

        return $this->Keycloak->getResponse($HttpRequest);
    }

    /**
     * @param string $resourceId
     * @return ResponseInterface
     * @throws IdentityProviderException
     * @throws \Exception
     */
    protected function getResource(string $resourceId): ResponseInterface
    {
        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "GET",
                $this->getEndpoint()."/".$resourceId,
                $this->getHttpRequestHeaders()
            );

        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);

        if ($HttpResponse->getStatusCode() == "200")
        {
            return $HttpResponse;
        }

        throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param string $ResourceAsString
     * @return bool
     * @throws IdentityProviderException
     * @throws \Exception
     */
    protected function addResource(string $ResourceAsString): bool
    {
        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "POST",
                $this->getEndpoint(),
                $this->getHttpRequestHeaders(),
                $ResourceAsString
            );

        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);

        if ($HttpResponse->getStatusCode() == "201")
        {
            return true;
        }

        throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param string $resourceId
     * @return bool
     * @throws IdentityProviderException
     * @throws \Exception
     */
    protected function deleteResource(string $resourceId): bool
    {
        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "DELETE",
                $this->getEndpoint()."/".$resourceId,
                $this->getHttpRequestHeaders()
            );

        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);

        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }

        throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param string $resourceId
     * @param string $ResourceAsString
     * @return bool
     * @throws IdentityProviderException
     * @throws \Exception
     */
    protected function updateResource(string $resourceId, string $ResourceAsString): bool
    {
        $HttpRequest = $this->Keycloak
            ->getRequestFactory()
            ->getRequest(
                "PUT",
                $this->getEndpoint()."/".$resourceId,
                $this->getHttpRequestHeaders(),
                $ResourceAsString
            );

        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);

        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }

        throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @return string[]
     * @throws IdentityProviderException
     */
    protected function getHttpRequestHeaders(): array
    {
        $Token = $this->getAccessToken()->getToken();
        return [
            "Authorization"=>"Bearer ".$Token,
            "Content-Type"=>"application/json"
        ];
    }
}
