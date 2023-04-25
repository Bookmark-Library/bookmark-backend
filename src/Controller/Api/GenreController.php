<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class genreController extends AbstractController
{
    /**
     * Retourne les données en JSON
     * 
     * @Route("/api/genres", name="app_api_genres_get", methods={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();

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
                    'get_genres_collection'
                ]
            ]
        );
    }

    /**
     * @Route("/api/genres/{id<\d+>}", name="app_api_genres_get_item", methods={"GET"})
     */
    public function getItem(Genre $genre = null)
    {
        if ($genre === null) {
            return $this->json(
                ['error' => 'Genre non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $genre,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_genres_collection'
                ]
            ]
        );
    }
}
