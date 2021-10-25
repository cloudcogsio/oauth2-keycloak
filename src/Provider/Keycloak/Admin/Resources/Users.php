<?php
namespace Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Resources;

use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\UserRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\UserConsentRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\CredentialRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\FederatedIdentityRepresentation;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\ClientFactory;
use Cloudcogs\OAuth2\Client\Provider\Keycloak\Admin\Definitions\GroupRepresentation;

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
    
    public function getUsers(array $params = []) : array
    {
        $validated = $this->validateParams($params);
        $users = [];
        
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."?".http_build_query($validated),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function getUser(string $Id) : UserRepresentation
    {        
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("GET", $this->getEndpoint()."/".$Id,
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "200")
        {
            $userData = json_decode((string) $HttpResponse->getBody(), true);
            return $this->hydrate($userData);
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function addUser(UserRepresentation $User)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("POST", $this->getEndpoint(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], $User->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "201")
        {
            $newUser = $this->getUsers([Users::PARAM_BRIEF_REPRESENTATION => false, Users::PARAM_EMAIL => $User->getEmail()]);
            if (is_array($newUser) && count($newUser) === 1) return $newUser[0];
            
            return true;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function updateUser(UserRepresentation $User)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("PUT", $this->getEndpoint()."/".$User->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ], $User->__toString());
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return $this->getUser($User->getId());
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function deleteUser(UserRepresentation $User)
    {
        $Token = $this->getAccessToken()->getToken();
        $HttpRequest = $this->Keycloak->getRequestFactory()->getRequest("DELETE", $this->getEndpoint()."/".$User->getId(),
            [
                "Authorization"=>"Bearer ".$Token,
                "Content-Type"=>"application/json"
            ]);
        
        $HttpResponse = $this->Keycloak->getResponse($HttpRequest);
        
        if ($HttpResponse->getStatusCode() == "204")
        {
            return true;
        }
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function getGroupMemberships(UserRepresentation $User, array $params = [])
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function addGroupMembership(UserRepresentation $User, GroupRepresentation $Group)
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    public function deleteGroupMembership(UserRepresentation $User, GroupRepresentation $Group)
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
    /**
     * 
     * @param UserRepresentation $User
     * @throws \Exception
     * @return bool
     */
    public function sendVerificationEmail(UserRepresentation $User, string $clientId = "", string $redirectUri = "")
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
        else {
            throw new \Exception($HttpResponse->getReasonPhrase(), $HttpResponse->getStatusCode());
        }
    }
    
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
        
        return new UserRepresentation($userData);
    }
}
