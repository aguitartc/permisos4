<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;


class LogoutUpcListener
{
    protected $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $sso = 'sso.upc.edu';
        $context = '/CAS';

        if(strpos($this->request, 'devsi') !== false || strpos($this->request, 'dev.utgaeib') !== false)
            $sso = 'sso.pre.upc.edu';

        $urlLogoutCas = "https://".$sso.$context."/logout?url=" . urlencode(str_replace("/logout", "", $this->request->getUri()));

        $logoutEvent->setResponse(new RedirectResponse($urlLogoutCas, Response::HTTP_MOVED_PERMANENTLY));
    }
}