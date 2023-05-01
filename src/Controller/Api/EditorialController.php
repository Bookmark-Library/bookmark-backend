<?php

namespace App\Controller\Api;

use App\Repository\EditorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorialController extends AbstractController
{
    /**
     * Get the editorial for home page chosen by redactors
     * 
     * @Route("/api/editorials", name="app_api_editorials_get_item", methods={"GET"})
     */
    public function getItemForHome(EditorialRepository $editorialRepository)
    {
        $editorials = $editorialRepository->findByHomeActive();

        return $this->json(
            $editorials,
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'get_editorials_collection',
                ]
            ]
        );
    }
}
