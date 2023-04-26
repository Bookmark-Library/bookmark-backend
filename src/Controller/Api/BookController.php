<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Library;
use App\Repository\BookRepository;
use App\Repository\LibraryRepository;
use App\Service\ApiManager;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
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
     * @Route("/api/books/{id<\d+>}", name="app_api_books_get_item", methods={"GET"})
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

    /**
     * Create book item 
     * 
     * @Route("/api/books", name="app_api_books_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, SluggerInterface $slugger)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        $library = new Library();

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
            // @Retourner des erreurs de validation propres
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager = $doctrine->getManager();
        $book->setSlug($slugger->slug($book->getTitle())->lower());
        $entityManager->persist($book);

        $library->setUser($user);
        $library->setBook($book);
        $entityManager->persist($library);

        $entityManager->flush();

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
     * Create book item by ISBN with BNF API
     * 
     * @Route("/api/books/isbn", name="app_api_books_isbn_post", methods={"POST"})
     */
    public function createItemByIsbn(Request $request, DenormalizerInterface $denormalizerInterface, ManagerRegistry $doctrine, ApiManager $apiManager, ValidatorInterface $validator, BookRepository $bookRepository, LibraryRepository $libraryRepository, SluggerInterface $slugger)
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        // JSON with ISBN 
        $jsonContent = $request->getContent();
        $isbn = json_decode($jsonContent)->isbn;

        // Fetch By given ISBN
        $xml = $apiManager->fetchByISBN($isbn);

        $bookArray = $apiManager->getBook($xml);

        //dd($bookArray);

        try {
            $book = $denormalizerInterface->denormalize($bookArray, Book::class);
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'Tableau invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($book);

        $entityManager = $doctrine->getManager();

        
        $existingBookArray = $bookRepository->findByIsbn($book->getIsbn());
        $existingBook = $existingBookArray[0];
        if($existingBook){
            $library = new Library();
            $existingLibrary = $libraryRepository->findByLibrary($user, $existingBook);

            if($existingLibrary){
                return $this->json(
                    ['error' => 'Livre déjà dans la bibliothèque'],
                    Response::HTTP_CONFLICT
                );
    
            }

            $library->setUser($user);
            $library->setBook($existingBook);
            $entityManager->persist($library);
            $entityManager->flush();
    
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
        
        if (count($errors) > 0) {
            $errorsClean = [];
            // @Retourner des erreurs de validation propres
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);

        }
    
        $book->setSlug($slugger->slug($book->getTitle())->lower());
        $entityManager->persist($book);

        $library = new Library();
        $library->setUser($user);
        $library->setBook($book);
        $entityManager->persist($library);
        $entityManager->flush();

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
