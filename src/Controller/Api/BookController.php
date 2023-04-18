<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * Retourne les données en JSON
     * 
     * @Route("/api/books", name="app_api_books_get", methods={"GET"})
     */
    public function getCollection(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        // @see https://symfony.com/doc/5.4/controller.html#returning-json-response
        return $this->json(
            // données à convertir/serialiser
            $books,
            // status code
            Response::HTTP_OK,
            // header
            [],
            // options à transmettre au Serializer
            ['groups' => [
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]
            ]
        );
    }

    /**
     * @Route("/api/books/{id<\d+>}", name="app_api_books_get_item")
     */
    public function getItem(Book $book = null)
    {
        if ($book === null) {
            return $this->json(
                ['error' => 'Livre non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $book,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_books_collection',
                    'get_authors_collection',
                    'get_genres_collection'
                ]
            ]
        );
    }

}
