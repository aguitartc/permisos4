<?php


namespace App\Utils;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class LogoutCasUpc implements LogoutSuccessHandlerInterface
{
    /**
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onLogoutSuccess(Request $request)
    {
        $sso = 'sso.upc.edu';
        $context = '/CAS';

        if(strpos($request, 'devsi') !== false || strpos($request, 'dev.utgaeib') !== false)
            $sso = 'sso.pre.upc.edu';

        $urlLogoutCas = "https://".$sso.$context."/logout?url=" . urlencode(str_replace("/logout", "", $request->getUri()));
        return new RedirectResponse($urlLogoutCas);;
    }
}