<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Library;
use App\Repository\BookRepository;
use App\Repository\LibraryRepository;
use App\Service\ApiManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookController extends AbstractController
{
    /**
     * Get all books in JSON 
     * 
     * @Route("/api/books", name="app_api_books_get_collection", methods={"GET"})
     */
    public function getCollection(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->json(
            // data to serialize
            $books,
            // status code
            Response::HTTP_OK,
            // header
            [],
            // options to send to Serializer
            [
                'groups' => [
                    'get_books_collection',
                    'get_authors_collection',
                    'get_genres_collection'
                ]
            ]
        );
    }

    /**
     * Get a given book in JSON 
     * 
     * @Route("/api/books/{id<\d+>}", name="app_api_books_get_item", methods={"GET"})
     */
    public function getItem(Book $book = null): Response
    {
        // Book from ParamConverter
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

    /**
     * Create a book 
     * 
     * @Route("/api/books", name="app_api_books_create", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        $jsonContent = $request->getContent();

        try {
            $book = $serializer->deserialize($jsonContent, Book::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            $errorsClean = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->setSlug($slugger->slug($book->getTitle())->lower());
        $em->persist($book);

        $library = new Library();
        $library->setUser($user);
        $library->setBook($book);
        $em->persist($library);

        $em->flush();

        return $this->json(
            $book,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_books_get_item', ['id' => $book->getId()])
            ],
            ['groups' => [
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]]
        );
    }

    /**
     * Create a book by ISBN with BNF API
     * 
     * @Route("/api/books/isbn", name="app_api_books_create_isbn", methods={"POST"})
     */
    public function createItemByIsbn(Request $request, DenormalizerInterface $denormalizerInterface, EntityManagerInterface $em, ApiManager $apiManager, ValidatorInterface $validator, BookRepository $bookRepository, LibraryRepository $libraryRepository, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Get ISBN from JSON 
        $jsonContent = $request->getContent();
        $isbn = json_decode($jsonContent)->isbn;

        // Fetch By given ISBN with ApiManager Service
        // Get a bookArray with data providing from BNf API
        $xml = $apiManager->fetchByISBN($isbn);
        $bookArray = $apiManager->getBook($xml);

        try {
            $book = $denormalizerInterface->denormalize($bookArray, Book::class);
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'Tableau invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Search if book isbn already exist in database and add it in User's Library
        $existingBookArray = $bookRepository->findByIsbn($book->getIsbn());

        if ($existingBookArray) {
            $existingBook = $existingBookArray[0];
            $existingLibrary = $libraryRepository->findByLibrary($user, $existingBook);

            if ($existingLibrary) {
              
                return $this->json(
                    ['error' => 'Livre déjà dans la bibliothèque'],
                    Response::HTTP_CONFLICT
                );
            }

            $library = new Library();
            $library->setUser($user);
            $library->setBook($existingBook);
            $em->persist($library);
            $em->flush();

            return $this->json(
                $book,
                Response::HTTP_CREATED,
                [
                    'Location' => $this->generateUrl('app_api_books_get_item', ['id' => $existingBook->getId()])
                ],
                ['groups' => [
                    'get_books_collection',
                    'get_authors_collection',
                    'get_genres_collection'
                ]]
            );
        }

        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            $errorsClean = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->setSlug($slugger->slug($book->getTitle())->lower());
        $em->persist($book);

        $library = new Library();
        $library->setUser($user);
        $library->setBook($book);
        $em->persist($library);

        $em->flush();

        return $this->json(
            $book,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_books_get_item', ['id' => $book->getId()])
            ],
            ['groups' => [
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]]
        );
    }
}
