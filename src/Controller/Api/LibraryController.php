<?php

namespace App\Controller\Api;

use App\Entity\Library;
use App\Repository\GenreRepository;
use App\Repository\LibraryRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * Get all libraries for connected user
     * 
     * @Route("/api/libraries/", name="app_api_libraries_get_collection", methods={"GET"})
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
     * Update the library of given Book for connected User
     * 
     * @Route("/api/libraries/{id<\d+>}", name="app_api_libraries_update", methods={"PUT"})
     */
    public function updateItem(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, Library $library, GenreRepository $genreRepository)
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

        // Get genre_is from JSON Reponse and set it for User's selected genre and Book's genre
        $contentForBook = json_decode($request->getContent(), true);
        $genreId = $contentForBook["genre_id"];
        if ($genreId !== null) {
            $genre = $genreRepository->find($genreId);
            $book = $library->getBook();
            $book->addGenre($genre);
            $library->setGenre($genre);
        }

        $em->persist($library);

        $em->flush();

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
     * Delete given book from User's Library
     * 
     * @Route("/api/libraries/{id<\d+>}", name="app_api_libraries_delete", methods={"DELETE"})
     */
    public function deleteItem(LibraryRepository $libraryRepository, Library $library)
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
