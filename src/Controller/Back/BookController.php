<?php

namespace App\Controller\Back;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/back/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="app_back_book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('back/book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_book_new", methods={"GET", "POST"})
     */
    public function new(Request $request, BookRepository $bookRepository, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setSlug($slugger->slug($book->getTitle())->lower());

            $bookRepository->add($book, true);

            $this->addFlash('success', "Le livre <b>{$book->getTitle()}</b> a bien été ajouté.");

            return $this->redirectToRoute('app_back_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('back/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_book_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Book $book, BookRepository $bookRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setSlug($slugger->slug($book->getTitle())->lower());

            $bookRepository->add($book, true);

            $this->addFlash('warning', "Le livre <b>{$book->getTitle()}</b> a bien été modifié.");

            return $this->redirectToRoute('app_back_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $bookRepository->remove($book, true);
            $this->addFlash('danger', "Le livre <b>{$book->getTitle()}</b> a bien été supprimé.");
        }

        return $this->redirectToRoute('app_back_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
