<?php

namespace App\Controller\Back;

use App\Entity\Editorial;
use App\Form\EditorialType;
use App\Repository\EditorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/editorial")
 */
class EditorialController extends AbstractController
{
    /**
     * @Route("/", name="app_back_editorial_index", methods={"GET"})
     */
    public function index(EditorialRepository $editorialRepository): Response
    {
        return $this->render('back/editorial/index.html.twig', [
            'editorials' => $editorialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_editorial_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EditorialRepository $editorialRepository): Response
    {
        $editorial = new Editorial();
        $form = $this->createForm(EditorialType::class, $editorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editorialRepository->add($editorial, true);

            $this->addFlash('success', "L'éditorial <b>{$editorial->getTitle()}</b> a bien été ajouté.");

            return $this->redirectToRoute('app_back_editorial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/editorial/new.html.twig', [
            'editorial' => $editorial,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/homeActive", name="app_back_editorial_home_active", methods={"GET", "POST"})
     */
    public function homeOrder(EditorialRepository $editorialRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {

            $editorials = $editorialRepository->findAll();
            foreach ($editorials as $editorial) {
                $editorial->setActive(0);
            }

            $editorial = $editorialRepository->find($request->request->get('active'));
            $editorial->setActive(1);
            $editorialRepository->add($editorial, true);

            return $this->redirectToRoute('app_back_editorial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/editorial/home_active.html.twig', [
            'editorials' => $editorialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_editorial_show", methods={"GET"})
     */
    public function show(Editorial $editorial): Response
    {
        return $this->render('back/editorial/show.html.twig', [
            'editorial' => $editorial,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_editorial_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Editorial $editorial, EditorialRepository $editorialRepository): Response
    {
        $form = $this->createForm(EditorialType::class, $editorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editorialRepository->add($editorial, true);

            $this->addFlash('warning', "L'éditorial <b>{$editorial->getTitle()}</b> a bien été modifié.");

            return $this->redirectToRoute('app_back_editorial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/editorial/edit.html.twig', [
            'editorial' => $editorial,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_editorial_delete", methods={"POST"})
     */
    public function delete(Request $request, Editorial $editorial, EditorialRepository $editorialRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $editorial->getId(), $request->request->get('_token'))) {
            $editorialRepository->remove($editorial, true);
            $this->addFlash('danger', "L'éditorial <b>{$editorial->getTitle()}</b> a bien été supprimé.");
        }

        return $this->redirectToRoute('app_back_editorial_index', [], Response::HTTP_SEE_OTHER);
    }
}
