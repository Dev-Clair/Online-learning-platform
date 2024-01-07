<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Courses;
use App\Entity\Enrollment;
use App\Form\ChapterType;
use App\Form\CoursesType;
use App\Repository\ChapterRepository;
use App\Repository\CoursesRepository;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
class CoursesController extends AbstractController
{
    public function __construct(private CacheInterface $redisCache)
    {
    }

    #[Route('/', name: 'app_courses_index', methods: ['GET'])]
    public function index(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
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

            $course->setUser($this->getUser());

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_index');
        }

        return $this->render('courses/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/manage', name: 'app_courses_manage', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function manage(CoursesRepository $coursesRepository): Response
    {
        $courses = $coursesRepository->findBy(['user' => $this->getUser()]);

        return $this->render('courses/manage.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/learning', name: 'app_courses_learning', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function learning(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findBy(['user' => $this->getUser()], ['enrolledDate' => 'ASC']);

        return $this->render('courses/learning.html.twig', [
            'enrollments' => $enrollments
        ]);
    }

    #[Route('/{id}', name: 'app_courses_show', methods: ['GET'])]
    public function show(Courses $course): Response
    {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
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

            return $this->redirectToRoute('app_courses_index');
        }

        return $this->render('courses/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/chapter', name: 'app_courses_chapter', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function chapter_index(Courses $course, ChapterRepository $chapterRepository): Response
    {
        $chapters = $chapterRepository->findBy(['courses' => $course]);

        if (!$chapters) {
            if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

                $this->addFlash('warning', 'You cannot access or modify this course.');

                return $this->redirectToRoute('app_courses_index');
            }

            $this->addFlash('error', 'No Chapters Have Been Created For '  . $course->getTitle() . ' Course. Kindly Create New');
        }

        return $this->render('courses/chapter/index.html.twig', [
            'chapters' => $chapters,
            'course_id' => $course->getId()
        ]);
    }

    #[Route('/{id}/chapter/new', name: 'app_courses_chapter_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function chapter_new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $chapter->setUser($this->getUser());

            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/chapter/{id}', name: 'app_courses_chapter_show', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function chapter_show(Chapter $chapter): Response
    {
        return $this->render('courses/chapter/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }

    #[Route('/{id}/chapter/{id}/edit', name: 'app_courses_chapter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function chapter_edit(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/chapter/{id}', name: 'app_courses_chapter_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function chapter_delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_chapter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/enroll', name: 'app_courses_enroll', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function enroll(Courses $course, EntityManagerInterface $entityManager): Response
    {
        $enrollment = new Enrollment;
        $enrollment->setEnrolledDate(new \DateTimeImmutable());
        $enrollment->setUser($this->getUser());
        $enrollment->setCourses($course);

        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Congratulations! You have enrolled for '  . $course->getTitle());

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{id}/unenroll', name: 'app_courses_unenroll', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function unenroll(Courses $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
    {
        $enrollment = $enrollmentRepository->findOneBy(['courses' => $course]);

        if ($enrollment) {
            $entityManager->remove($enrollment);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully unenrolled ' . $enrollment->getUser()->getFirstname() . ' from '  . $course->getTitle());
        }

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{id}/enrolled', name: 'app_courses_enrolled', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function enrolled(Courses $course, EnrollmentRepository $enrollmentRepository): Response
    {
        if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

            $this->addFlash('warning', 'You can only see students enrolled in your courses.');

            return $this->redirectToRoute('app_courses_index');
        }

        $enrollments = $enrollmentRepository->findBy(['courses' => $course]);

        return $this->render('courses/enrolled.html.twig', [
            'enrollments' => $enrollments ?? [],
        ]);
    }

    #[Route('/{id}/learning/lesson', name: 'app_courses_learning_lesson', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function learning_lesson(Courses $course): Response
    {
        return $this->render('courses/lesson.html.twig');
    }

    #[Route('/{id}', name: 'app_courses_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function delete(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

            $this->addFlash('warning', 'You are not authorized to delete this course.');

            return $this->redirectToRoute('app_courses_index');
        }

        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_index');
    }

    protected function last_accessed(Courses $course, CoursesRepository $coursesRepository, EntityManagerInterface $entityManager): Response
    {
        $course = $coursesRepository->findOneBy(['user' => $this->getUser(), 'courses' => $course]);

        $course->setLastAccessed(new \DateTimeImmutable());

        $entityManager->persist($course);
        $entityManager->flush();

        /**
         * Triggered pre log out
         */

        return $this->redirectToRoute('app_courses_learning');
    }

    protected function completed(Courses $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $course->getUser();
        $enrollment = $enrollmentRepository->findOneBy(['user' => $user, 'courses' => $course]);

        $enrollment->setCompletionDate(new \DateTimeImmutable());

        $entityManager->persist($enrollment);
        $entityManager->flush();

        /**
         * Triggered on course completion
         * 
         * Triggers course completion mail event
         */

        return $this->redirectToRoute('app_courses_learning');
    }
}
