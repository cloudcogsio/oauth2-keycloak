<?php
/**
 * Copyright 2022, Cloudcogs.io
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Ricardo Assing (ricardo@tsiana.ca)
 */

namespace Cloudcogs\OAuth2\Client\Provider\Keycloak;

use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\InvalidUrlException;
use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\TokenIntrospectionException;
use Cloudcogs\OAuth2\Client\OpenIDConnect\Exception\WellKnownEndpointException;
use Cloudcogs\OAuth2\Client\OpenIDConnect\ParsedToken;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PolicyEnforcer extends AbstractAuthorizationServices
{
    protected ServerRequestInterface $request;
    protected string $bearerToken;
    protected ParsedToken $parsedToken;

    protected ResourceManagement $ResourceManagement;
    protected ResourcePermission $permission;

    /**
     * @param ServerRequestInterface $request
     * @return $this
     * @throws InvalidUrlException
     * @throws WellKnownEndpointException
     */
    public function setRequest(ServerRequestInterface $request) : PolicyEnforcer {
        $this->request = $request;

        $this->ResourceManagement = $this->Keycloak->ResourceManagement();

        return $this;
    }

    /**
     * @throws IdentityProviderException
     * @throws TokenIntrospectionException
     * @throws Exception\AuthorizationTokenException
     */
    public function isGranted() : ResponseInterface {

        // Request has no bearer token in Authorization header, return 401
        if (!$this->isBearerTokenAvailable()) return new EmptyResponse(StatusCodeInterface::STATUS_UNAUTHORIZED, ['WWW-Authenticate'=>'Bearer']);

        $kcResourceId = $this->getKeycloakResourceId();

        // Unable to find a kc resource matched via requested URI, return 404
        if ($kcResourceId == null) return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);

        $permissions = $this->getKeycloakPermissions();

        // No permitted resources/permissions returned from keycloak, return 403
        if ($permissions == null) return new EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);

        /** @var $permission ResourcePermission */
        foreach ($permissions as $permission) {
            if ($permission->getResourceId() == $kcResourceId) {
                $this->permission = $permission;

                // kc returned a matching resource permission, return 200
                return new EmptyResponse(200);
            }
        }

        // No matching resource permission found in kc, return 403
        return new EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);
    }

    /**
     * Retrieve the matched resource permission after calling isGranted() and receiving a 200 response
     *
     * @return ResourcePermission|null
     */
    public function getGrantedKeycloakResourcePermission() : ?ResourcePermission {
        return $this->permission;
    }

    /**
     * @throws TokenIntrospectionException
     * @throws IdentityProviderException
     * @throws Exception\AuthorizationTokenException
     */
    public function getKeycloakPermissions(): ?array
    {
        $RPTRequest = new RequestingPartyTokenRequest($this->Keycloak, $this->bearerToken);
        $RPTRequest->setAudience($this->Keycloak->getAudienceFromKeycloakConfig())->setResponseMode();

        $RPTResponse = $this->Keycloak->getAuthorizationToken($RPTRequest);
        return $RPTResponse->getPermissions();
    }

    /**
     * @throws IdentityProviderException
     */
    protected function getKeycloakResourceId() : ?string {
        $resourceUri = $this->request->getUri()->getPath();

        $kcResource = $this->ResourceManagement->listResources(
            [ResourceManagement::QUERY_URI => $resourceUri],
            [ResourceManagement::PARAM_NAME_EXACT => "true"]
        );

        return (count($kcResource) === 1) ? $kcResource[0] : null;
    }

    /**
     * @return bool
     * @throws TokenIntrospectionException
     */
    protected function isBearerTokenAvailable() : bool {
        $AuthHeader = $this->request->getHeader('Authorization');
        if (!empty($AuthHeader))
        {
            $this->bearerToken = str_replace("Bearer ", "", $AuthHeader[0]);
            $this->parsedToken = $this->Keycloak->introspectToken($this->bearerToken);

            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getBearerToken() : ?string {
        return $this->bearerToken;
    }

    /**
     * @return ParsedToken|null
     */
    public function getParsedToken() : ?ParsedToken {
        return $this->parsedToken;
    }
}
