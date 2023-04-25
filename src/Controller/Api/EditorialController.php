<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorialController extends AbstractController
{
    /**
     * @Route("/api/editorial", name="app_api_editorial")
     */
    public function index(): Response
    {
        return $this->render('api/editorial/index.html.twig', [
            'controller_name' => 'EditorialController',
        ]);
    }
}
