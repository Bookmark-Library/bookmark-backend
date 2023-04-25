<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class genreController extends AbstractController
{
    /**
     * @Route("/api/genre", name="app_api_genre")
     */
    public function index(): Response
    {
        return $this->render('api/genre/index.html.twig', [
            'controller_name' => 'genreController',
        ]);
    }
}
