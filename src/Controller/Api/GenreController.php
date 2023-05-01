<?php

namespace App\Controller\Api;

use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * Get all genres in JSON
     * 
     * @Route("/api/genres", name="app_api_genres_get_collection", methods={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();

        return $this->json(
            $genres,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_genres_collection',
                    'get_books_collection',
                    'get_authors_collection',
                ]
            ]
        );
    }

    /**
     * Get genres for home page chosen by redactors
     * 
     * @Route("/api/genres/home", name="app_api_genres_get_home", methods={"GET"})
     */
    public function getHomeSelection(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findByHomeOrder();

        return $this->json(
            $genres,
            Response::HTTP_OK,
            [],
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
