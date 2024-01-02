<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chapter')]
class ChapterController extends AbstractController
{
    #[Route('/', name: 'app_chapter_index', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function index(ChapterRepository $chapterRepository): Response
    {
        $chapters = $chapterRepository->findBy(['user' => $this->getUser()]);

        return $this->render('chapter/index.html.twig', [
            'chapters' => $chapters
        ]);
    }

    #[Route('/new', name: 'app_chapter_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapter_show', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function show(Chapter $chapter): Response
    {
        return $this->render('chapter/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chapter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function edit(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapter_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chapter_index', [], Response::HTTP_SEE_OTHER);
    }
}
