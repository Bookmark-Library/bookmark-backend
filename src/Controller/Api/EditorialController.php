<?php

namespace App\Controller\Api;

use App\Entity\Editorial;
use App\Repository\EditorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorialController extends AbstractController
{
    /**
     * @Route("/api/editorials", name="app_api_editorials_get", methods={"GET"})
     */
    public function getCollection(EditorialRepository $editorialRepository): Response
    {
        $editorials = $editorialRepository->findByHomeActive();

        return $this->json(
            $editorials,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_editorials_collection',
                ]
            ]
        );
    }
}
