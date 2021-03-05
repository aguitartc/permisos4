<?php
// src/AppBundle/Security/TokenAuthenticator.php
//https://openclassrooms.com/forum/sujet/symfony-3-authentification-cas-formulaire

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Security;


class CasJasigAuthenticator extends AbstractGuardAuthenticator
{
    private $cas_host;
    private $cas_port;
    private $cas_context;
    private $cas_ca_chain;
    private $security;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * Process configuration
     * @param $cas_host
     * @param $cas_port
     * @param $cas_context
     * @param $cas_ca_chain
     * @param RouterInterface $router
     * @param Security $security
     */
    public function __construct($cas_host, $cas_port, $cas_context, $cas_ca_chain, RouterInterface $router, Security $security)
    {
    	$this->cas_host = $cas_host;
    	$this->cas_port = $cas_port;
    	$this->cas_context = $cas_context;
    	$this->cas_ca_chain = $cas_ca_chain;
    	$this->router = $router;
        $this->security = $security;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     * This will be called on every request and your job is to decide if the
     * authenticator should be used for this request (return true) or if it should be skipped (return false).
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        //if(!preg_match("^(/ca|/es|/en)/^", $request->getPathInfo()))
            //return false;

        // if there is already an authenticated user (likely due to the session)
        // then return false and skip authentication: there is no need.
        if ($this->security->getUser())
            return false;

        //The user is not logged in, so the authenticator should continue
        return true;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * This will be called on every request and your job is to read the token (or whatever
     * your "authentication" information is) from the request and return it.
     * These credentials are later passed as the first argument of getUser().
     * @param Request $request
     * @return string
     */
    public function getCredentials(Request $request)
    {
        \phpCAS::setVerbose(true);
        \phpCAS::client(SAML_VERSION_1_1, $this->cas_host, intval($this->cas_port), $this->cas_context);
        \phpCAS::setLang(PHPCAS_LANG_CATALAN);
        \phpCAS::setCasServerCACert($this->cas_ca_chain);
        //\phpCAS::setNoCasServerValidation();
        \phpCAS::handleLogoutRequests();
        \phpCAS::forceAuthentication();
        if (\phpCAS::getUser())
            return \phpCAS::getUser();
        return '';
    }

    /**
     * Calls the UserProvider providing a valid User
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return bool
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    /**
     * Mandatory but not in use in a remote authentication
     * @param $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Mandatory but not in use in a remote authentication
     * @param Request $request
     * @param TokenInterface $token
     * @param $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * Mandatory but not in use in a remote authentication
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

      	$url = $this->router->generate('security_denied');
      	return new RedirectResponse($url);
    }

    /**
     * Called when authentication is needed, redirect to your CAS server authentication form
     * @param Request $request
     * @param AuthenticationException|null $authException
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        //return new RedirectResponse($this->server_login_url.'?'.$this->query_service_parameter.'='.urlencode($request->getUri()));
        //error_log('start'.$request->getUri());
        //return new RedirectResponse('https://cas.upc.edu/login?service=' . urlencode($request->getUri()));
    }

    /**
     * Mandatory but not in use in a remote authentication
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
