<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * Retourne les données en JSON
     * 
     * @Route("/api/genres", name="app_api_genres_get", methods={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findByHomeOrder();

        // @see https://symfony.com/doc/5.4/controller.html#returning-json-response
        return $this->json(
            // données à convertir/serialiser
            $genres,
            // status code
            Response::HTTP_OK,
            // header
            [],
            // options à transmettre au Serializer
            [
                'groups' => [
                    'get_genres_collection',
                    'get_books_collection',
                    'get_authors_collection',
                ]
            ]
        );
    }
}
