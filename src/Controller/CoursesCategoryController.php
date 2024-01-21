<?php

namespace App\Controller;

use App\Entity\CoursesCategory;
use App\Form\CoursesCategoryType;
use App\Repository\CoursesCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses/category')]
#[IsGranted('ROLE_SUPER_INSTRUCTOR')]
class CoursesCategoryController extends AbstractController
{
    #[Route('/', name: 'app_courses_category_index', methods: ['GET'])]
    public function index(CoursesCategoryRepository $coursesCategoryRepository): Response
    {
        return $this->render('courses_category/index.html.twig', [
            'courses_categories' => $coursesCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_courses_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $coursesCategory = new CoursesCategory();
        $form = $this->createForm(CoursesCategoryType::class, $coursesCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($coursesCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses_category/new.html.twig', [
            'courses_category' => $coursesCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_courses_category_show', methods: ['GET'])]
    public function show(CoursesCategory $coursesCategory): Response
    {
        return $this->render('courses_category/show.html.twig', [
            'courses_category' => $coursesCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_courses_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CoursesCategory $coursesCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursesCategoryType::class, $coursesCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses_category/edit.html.twig', [
            'courses_category' => $coursesCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_courses_category_delete', methods: ['POST'])]
    public function delete(Request $request, CoursesCategory $coursesCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $coursesCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($coursesCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
