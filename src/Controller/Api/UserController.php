<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/{id<\d+>}", name="app_api_user_get_item")
     */
    public function getItem(User $user = null)
    {
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
}
