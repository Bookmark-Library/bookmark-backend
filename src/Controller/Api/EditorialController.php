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
     * Retourne les données en JSON
     * 
     * @Route("/api/editorials", name="app_api_editorials_get", methods={"GET"})
     */
    public function getCollection(EditorialRepository $editorialRepository): Response
    {
        $editorials = $editorialRepository->findAll();

        // @see https://symfony.com/doc/5.4/controller.html#returning-json-response
        return $this->json(
            // données à convertir/serialiser
            $editorials,
            // status code
            Response::HTTP_OK,
            // header
            [],
            // options à transmettre au Serializer
            [
                'groups' => [
                    'get_editorials_collection',
                ]
            ]
        );
    }

    /**
     * @Route("/api/editorials/{id<\d+>}", name="app_api_editorials_get_item", methods={"GET"})
     */
    public function getItem(Editorial $editorial = null)
    {
        if ($editorial === null) {
            return $this->json(
                ['error' => 'Editorial non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $editorial,
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
