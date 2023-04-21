<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/", name="app_api_users_get_item")
     */
    public function getItem()
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
                ]
            ]
        );
    }

    /**
     * Create user item
     * 
     * @Route("/api/users", name="app_api_users_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher)
    {
        $jsonContent = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsClean = [];
            // @Retourner des erreurs de validation propres
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Password hashed
        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_api_users_get_item')
            ],
            ['groups' => [
                'get_users_item'
            ]]
        );
    }

    /**
     * Update user item
     * 
     * @Route("/api/users", name="app_api_users_update", methods={"PUT"})
     */
    public function updateItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher)
    {
        /** @var \App\Entity\User $connectedUser */
        $connectedUser = $this->getUser();

        if ($connectedUser === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        $jsonContent = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsClean = [];
            // @Retourner des erreurs de validation propres
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Password hashed
        if ($user->getPassword()) {
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        // Update connected User
        $connectedUser->setEmail($user->getEmail());
        $connectedUser->setAlias($user->getAlias());
        $connectedUser->setAvatar($user->getAvatar());
        $connectedUser->setPassword($user->getPassword());

        $entityManager = $doctrine->getManager();
        $entityManager->persist($connectedUser);
        $entityManager->flush();

        return $this->json(
            $connectedUser,
            Response::HTTP_OK,
            [
                'Location' => $this->generateUrl('app_api_users_get_item')
            ],
            ['groups' => [
                'get_users_item'
            ]]
        );
    }

    /**
     * Delete user item
     * 
     * @Route("/api/users", name="app_api_users_delete", methods={"DELETE"})
     */
    public function deleteItem(UserRepository $userRepository)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }

        $userRepository->remove($user, true);

        return $this->json(
            $user,
            Response::HTTP_NO_CONTENT,
            [],
            [],
        );
    }
}
