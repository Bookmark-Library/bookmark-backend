<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Library;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class LibraryController extends AbstractController
{
    /**
     * @Route("/api/libraries/", name="app_api_libraries_get_collection")
     */
    public function getCollection()
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_users_item',
                    'get_library_collection',
                    'get_books_collection',
                    'get_authors_collection',
                    'get_genres_collection'
                ]
            ]
        );
    }

    /**
     * Create library item
     * 
     * @Route("/api/libraries", name="app_api_libraries_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, BookRepository $bookRepository, GenreRepository $genreRepository)
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

        // Getting Book for Library
        $contentForBook = json_decode($request->getContent(), true);
        $bookId = $contentForBook["book_id"];
        $book = $bookRepository->find($bookId);
        $genreId = $contentForBook["genre_id"];
        $genre = $genreRepository->find($genreId);

        try {
            $library = $serializer->deserialize($jsonContent, Library::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($library);

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
        $book->addGenre($genre);

        $library->setUser($user);
        $library->setBook($book);
        $library->setGenre($genre);
        $entityManager->persist($library);
        $entityManager->flush();

        return $this->json(
            $library,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_libraries_get_collection')
            ],
            ['groups' => [
                'get_users_item',
                'get_library_collection',
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]]
        );
    }

    /**
     * Update library item
     * 
     * @Route("/api/libraries/{id<\d+>}", name="app_api_libraries_update", methods={"PUT"})
     */
    public function updateItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, Library $library, GenreRepository $genreRepository, BookRepository $bookRepository)
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

        $contentForBook = json_decode($request->getContent(), true);
        $genreId = $contentForBook["genre_id"];
        if ($genreId !== null) {
            $genre = $genreRepository->find($genreId);
            $book = $library->getBook();
            $book->addGenre($genre);
            $library->setGenre($genre);
        }

        try {
            $newLibrary = $serializer->deserialize($jsonContent, Library::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($library);

        if (count($errors) > 0) {
            $errorsClean = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $library->setComment($newLibrary->getComment());
        $library->setFavorite($newLibrary->isFavorite());
        $library->setFinished($newLibrary->isFinished());
        $library->setPurchased($newLibrary->isPurchased());
        $library->setQuote($newLibrary->getQuote());
        $library->setRate($newLibrary->getRate());
        $library->setWishlist($newLibrary->isWishlist());

        $entityManager = $doctrine->getManager();
        $entityManager->persist($library);
        $entityManager->flush();

        return $this->json(
            $library,
            Response::HTTP_OK,
            [
                'Location' => $this->generateUrl('app_api_users_get_item')
            ],
            ['groups' => [
                'get_users_item',
                'get_library_collection',
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]]
        );
    }


    /**
     * Delete library item
     * 
     * @Route("/api/libraries/{id<\d+>}", name="app_api_libraries_delete", methods={"DELETE"})
     */
    public function deleteItem(Request $request, LibraryRepository $libraryRepository, Library $library)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        $libraryRepository->remove($library, true);

        return $this->json(
            $user,
            Response::HTTP_NO_CONTENT,
            [],
            ['groups' => [
                'get_users_item',
                'get_library_collection',
                'get_books_collection',
                'get_authors_collection',
                'get_genres_collection'
            ]]
        );
    }
}
