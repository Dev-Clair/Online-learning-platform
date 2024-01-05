<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Enrollment;
use App\Form\CoursesType;
use App\Repository\CoursesRepository;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
class CoursesController extends AbstractController
{
    #[Route('/', name: 'app_courses_index', methods: ['GET'])]
    public function index(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_courses_show', methods: ['GET'])]
    public function show(Courses $course): Response
    {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/instructor', name: 'app_courses_instructor', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function instructor(CoursesRepository $coursesRepository): Response
    {
        $courses = $coursesRepository->findBy(['user' => $this->getUser()]);

        return $this->render('courses/instructor.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_courses_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function edit(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

            $this->addFlash('warning', 'You are not authorized to edit this course.');

            return $this->redirectToRoute('app_courses_index');
        }

        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $course->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_courses_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Courses();
        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $course->setTitle(ucwords($form->get('title')->getData()));

            $course->setDescription(ucwords($form->get('description')->getData()));

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_INSTRUCTOR')]
    #[Route('/{id}/unenroll', name: 'app_courses_unenroll', methods: ['GET'])]
    public function unenroll(Courses $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
    {
        // $user = $this->getUser();
        // $enrollment = $enrollmentRepository->findOneBy(['user' => $user, 'courses' => $course]);

        // if ($enrollment) {
        //     $entityManager->remove($enrollment);
        //     $entityManager->flush();

        //     $this->addFlash('success', 'Successfully unenrolled ' . $enrollment->getUser()->getFirstname() . ' from '  . $course->getTitle());
        // }

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{id}', name: 'app_courses_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function delete(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_STUDENT')]
    #[Route('/{id}/enroll', name: 'app_courses_enroll', methods: ['GET'])]
    public function enroll(Courses $course, EntityManagerInterface $entityManager): Response
    {
        $enrollment = new Enrollment;
        $enrollment->setEnrolledDate(new \DateTimeImmutable());
        $enrollment->setUser($this->getUser());
        $enrollment->setCourses($course);

        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully enrolled for '  . $course->getTitle());

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/learning', name: 'app_courses_learning', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function learning(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findBy(['user' => $this->getUser()]);

        return $this->render('courses/learning.html.twig', [
            'enrollments' => $enrollments
        ]);
    }

    #[Route('/{id}/learning/lesson', name: 'app_courses_learning_lesson', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function learning_lesson(Courses $course, EntityManagerInterface $entityManager): Response
    {
        return $this->render('courses/lesson.html.twig');
    }

    protected function completed(Courses $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $enrollment = $enrollmentRepository->findOneBy(['user' => $user, 'courses' => $course]);

        $enrollment->setCompletionDate(new \DateTimeImmutable());

        $entityManager->persist($enrollment);
        $entityManager->flush();

        /**
         * Triggered on course completion
         * 
         * Triggers course completion mail event
         */

        return $this->redirectToRoute('app_courses_student');
    }
}
