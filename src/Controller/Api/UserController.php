<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * Get connected user's data in JSON
     * 
     * @Route("/api/secure/users/", name="app_api_users_get_item", methods={"GET"})
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
     * Create an user
     * 
     * @Route("/api/users", name="app_api_users_create", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher)
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
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Password hashed
        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

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
     * Update connected user's data
     * 
     * @Route("/api/secure/users", name="app_api_users_update", methods={"PUT"})
     */
    public function updateItem(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
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
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update connected User
        $connectedUser->setEmail($user->getEmail());
        $connectedUser->setAlias($user->getAlias());
        $connectedUser->setAvatar($user->getAvatar());

        $em->persist($connectedUser);
        $em->flush();

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
     * Update connected user's password
     * 
     * @Route("/api/secure/users/password", name="app_api_users_password_update", methods={"PUT"})
     */
    public function updatePassword(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher)
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

        // get currentPassword from JSON's Response and check it
        $contentForPassword = json_decode($request->getContent(), true);
        $currentPassword = $contentForPassword["password_check"];
        
        if(!password_verify($currentPassword, $connectedUser->getPassword())){
            return $this->json(
                ['error' => 'Ancien mot de passe invalide !'],
                Response::HTTP_CONFLICT
            );
        }

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
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // Password hashed
        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());

        // Update connected User's password
        $connectedUser->setPassword($hashedPassword);
        $em->persist($connectedUser);
        $em->flush();

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
     * Update connected user's avatar
     * 
     * @Route("/api/secure/users/avatar", name="app_api_users_avatar_update", methods={"POST"})
     */
    public function updateAvatar(Request $request, ParameterBagInterface $params, EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $connectedUser */
        $connectedUser = $this->getUser();

        if ($connectedUser === null) {
            return $this->json(
                ['error' => 'Utilisateur non trouvé !'],
                Response::HTTP_NOT_FOUND
            );
        }
       
        // get image file
        $image = $request->files->get('file');

        // rename file
        $fileName = uniqid().'.' . $image->getClientOriginalName();

        // save image in avatar's directory
        $image->move($params->get('avatars_directory'), $fileName);

        // set avatar's path in User entity
        $connectedUser->setAvatar($fileName);
        $em->persist($connectedUser);
        $em->flush();

        return $this->json([
            'message' => 'Image uploaded successfully.'
        ]);
    }


    /**
     * Delete an user
     * 
     * @Route("/api/secure/users", name="app_api_users_delete", methods={"DELETE"})
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
