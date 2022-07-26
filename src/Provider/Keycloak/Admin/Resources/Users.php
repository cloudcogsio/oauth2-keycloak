<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\UserRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\UserConsentRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\CredentialRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\FederatedIdentityRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\GroupRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Exception\ApiResourceNotFoundException;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * https://www.keycloak.org/docs-api/18.0/rest-api/index.html#_users_resource
 */
class Users extends AbstractApiResource
{
    const PARAM_BRIEF_REPRESENTATION = "briefRepresentation";
    const PARAM_EMAIL = "email";
    const PARAM_EMAIL_VERIFIED = "emailVerified";
    const PARAM_ENABLED = "enabled";
    const PARAM_EXACT = "exact";    
    const PARAM_FIRST = "first";
    const PARAM_FIRST_NAME = "firstName";
    const PARAM_IDP_ALIAS = "idpAlias";
    const PARAM_IDP_USER_ID = "idpUserId";
    const PARAM_LAST_NAME = "lastName";
    const PARAM_MAX = "max";
    const PARAM_SEARCH = "search";
    const PARAM_USERNAME = "username";

    /**
     * @param array $params
     * @return array
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function getUsers(array $params = []) : array
    {
        $users = [];
        $HttpResponse = $this->getResourceData($params);

        if ($HttpResponse->getStatusCode() == "200")
        {
            $usersData = json_decode((string) $HttpResponse->getBody(), true);
            if (is_array($usersData))
            {
                foreach ($usersData as $userData)
                {
                    $users[] = $this->hydrate($userData);
                }
            }

            return $users;
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param string $Id
     * @return UserRepresentation
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function getUser(string $Id) : ?UserRepresentation
    {
        $HttpResponse = $this->getResource($Id);
        $userData = json_decode((string) $HttpResponse->getBody(), true);

        if (!is_array($userData)) return null;

        return $this->hydrate($userData);
    }

    /**
     * @param UserRepresentation $User
     * @return UserRepresentation|null
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function addUser(UserRepresentation $User) : ?UserRepresentation
    {
        $this->addResource($User->__toString());

        $newUser = $this->getUsers([Users::PARAM_BRIEF_REPRESENTATION => false, Users::PARAM_EMAIL => $User->getEmail()]);
        if (count($newUser) === 1)
            return $newUser[0];
            
        return null;
    }

    /**
     * @param UserRepresentation $User
     * @return UserRepresentation
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function updateUser(UserRepresentation $User): UserRepresentation
    {
        $this->updateResource($User->getId(), $User->__toString());
        return $this->getUser($User->getId());
    }

    /**
     * @param UserRepresentation $User
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function deleteUser(UserRepresentation $User): bool
    {
        return $this->deleteResource($User->getId());
    }

    /**
     * @param UserRepresentation $User
     * @param array $params
     * @return array
     * @throws ApiResourceNotFoundException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function getGroupMemberships(UserRepresentation $User, array $params = []): array
    {
        $groups = [];
        $validated = $this->validateParams($params);
        
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."/".$User->getId()."/groups?".http_build_query($validated),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $Groups = (new ClientFactory())($this->Keycloak, ClientFactory::RESOURCE_GROUPS);
            $groupsData = json_decode((string) $HttpResponse->getBody(), true);
            if (is_array($groupsData))
            {
                return $Groups->recursiveHydrate($groupsData);
            }
            
            return $groups;
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param UserRepresentation $User
     * @param GroupRepresentation $Group
     * @return array
     * @throws ApiResourceNotFoundException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function addGroupMembership(UserRepresentation $User, GroupRepresentation $Group): array
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getEndpoint()."/".$User->getId()."/groups/".$Group->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return $this->getGroupMemberships($User);
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param UserRepresentation $User
     * @param GroupRepresentation $Group
     * @return array
     * @throws ApiResourceNotFoundException
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function deleteGroupMembership(UserRepresentation $User, GroupRepresentation $Group): array
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getEndpoint()."/".$User->getId()."/groups/".$Group->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return $this->getGroupMemberships($User);
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param UserRepresentation $User
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function logout(UserRepresentation $User) : bool
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getEndpoint()."/".$User->getId()."/logout",
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param UserRepresentation $User
     * @param string $password
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function resetPassword(UserRepresentation $User, string $password) : bool
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getEndpoint()."/".$User->getId()."/reset-password",
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], json_encode(["value" => $password]));
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param UserRepresentation $User
     * @param string $clientId
     * @param string $redirectUri
     * @return bool
     * @throws IdentityProviderException
     * @throws Exception
     */
    public function sendVerificationEmail(UserRepresentation $User, string $clientId = "", string $redirectUri = ""): bool
    {
        $params = [];
        if ($clientId) $params['client_id'] = $clientId;
        if ($redirectUri) $params['redirect_uri'] = $redirectUri;
        
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getEndpoint()."/".$User->getId()."/send-verify-email?".http_build_query($params),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }

        throw new Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
    }

    /**
     * @param array $userData
     * @return UserRepresentation
     */
    public function hydrate(array $userData) : UserRepresentation
    {
        if (array_key_exists(UserRepresentation::CLIENT_CONSENTS, $userData) && !empty($userData[UserRepresentation::CLIENT_CONSENTS]))
        {
            foreach ($userData[UserRepresentation::CLIENT_CONSENTS] as $i => $UserConsentData)
            {
                $userData[UserRepresentation::CLIENT_CONSENTS][$i] = new UserConsentRepresentation($UserConsentData);
            }
        }
        
        if (array_key_exists(UserRepresentation::CREDENTIALS, $userData) && !empty($userData[UserRepresentation::CREDENTIALS]))
        {
            foreach ($userData[UserRepresentation::CREDENTIALS] as $i => $CredentialRepresentationData)
            {
                $userData[UserRepresentation::CREDENTIALS][$i] = new CredentialRepresentation($CredentialRepresentationData);
            }
        }
        
        if (array_key_exists(UserRepresentation::FEDERATED_IDENTITIES, $userData) && !empty($userData[UserRepresentation::FEDERATED_IDENTITIES]))
        {
            foreach ($userData[UserRepresentation::FEDERATED_IDENTITIES] as $i => $FederatedIdentityData)
            {
                $userData[UserRepresentation::FEDERATED_IDENTITIES][$i] = new FederatedIdentityRepresentation($FederatedIdentityData);
            }
        }
        
        return new UserRepresentation($userData, $this);
    }
}
