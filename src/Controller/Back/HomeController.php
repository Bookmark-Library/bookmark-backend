<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_back_home")
     */
    public function index(Request $request): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->render('back/home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        };

        return $this->redirectToRoute('app_login');
    }
}
