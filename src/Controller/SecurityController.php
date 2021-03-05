<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/denied", name="security_denied")
     *
     */
    public function deniedAction()
    {
        return $this->render('denied.html.twig',[
            'message'=>'access_denied',
            'message_image'=>'Stop-Sign-icon.png',
        ]);
    }


    /**
     * @Route("/", name="security_home")
     *
     */
    public function homeAction()
    {
        return $this->render('homepage.html.twig');
    }


    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }
}