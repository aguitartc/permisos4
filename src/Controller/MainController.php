<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param UserRepository $repository
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepage(UserRepository $repository, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        /*$user->setLastUpdate(new \DateTime());
        $em->persist($user);
        $em->flush();*/
        return $this->render('homepage.html.twig');
    }
}