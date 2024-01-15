<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Courses;
use App\Entity\Enrollment;
use App\Entity\Lesson;
use App\Entity\Reviews;
use App\Form\ChapterType;
use App\Form\CoursesType;
use App\Form\LessonType;
use App\Form\ReviewsType;
use App\Repository\ChapterRepository;
use App\Repository\CoursesRepository;
use App\Repository\EnrollmentRepository;
use App\Repository\LessonRepository;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
class CoursesController extends AbstractController
{
    public function __construct(private CacheInterface $redisCache)
    {
    }

    #[Route('/', name: 'app_courses_index', methods: ['GET'])]
    public function courses_index(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_courses_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_new(Request $request, EntityManagerInterface $entityManager): Response
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
    public function courses_manage(CoursesRepository $coursesRepository): Response
    {
        $courses = $coursesRepository->findBy(['user' => $this->getUser()]);

        return $this->render('courses/manage.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/learning', name: 'app_courses_learning', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_learning(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findBy(['user' => $this->getUser()], ['enrolledDate' => 'ASC']);

        return $this->render('courses/learning.html.twig', [
            'enrollments' => $enrollments
        ]);
    }

    #[Route('/{slug}', name: 'app_courses_show', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    public function courses_show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course
    ): Response {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_courses_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EntityManagerInterface $entityManager
    ): Response {
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

    /*
    *
    * ************************************* Start:: Chapters CRUD ****************************
    *
    */
    #[Route('/{slug}/chapter', name: 'app_courses_chapter_index', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_index(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        ChapterRepository $chapterRepository
    ): Response {
        $chapters = $chapterRepository->findBy(['courses' => $course, 'user' => $this->getUser()]);

        if (!$chapters) {
            if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

                $this->addFlash('warning', 'You cannot access or modify this course.');

                return $this->redirectToRoute('app_courses_index');
            }

            $this->addFlash('error', 'No Chapters Have Been Created For '  . $course->getTitle() . ' Course. Kindly Create New');
        }

        return $this->render('courses/chapter/index.html.twig', [
            'chapters' => $chapters,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/chapter/new', name: 'app_courses_chapter_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_new(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EntityManagerInterface $entityManager
    ): Response {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $chapter->setTitle(ucwords($form->get('title')->getData()));

            $chapter->setCourses($course);

            $chapter->setUser($this->getUser());

            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}', name: 'app_courses_chapter_show', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter
    ): Response {
        return $this->render('courses/chapter/show.html.twig', [
            'chapter' => $chapter,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}/edit', name: 'app_courses_chapter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
            'course_slug' => $course->getSlug()
        ]);
    }

    /*
    *
    * ************************************* Start:: Lessons CRUD ****************************
    *
    */
    #[Route('/{slug}/chapter/{chapterslug}/lesson', name: 'app_courses_lesson_index', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_index(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        LessonRepository $lessonRepository
    ): Response {

        $lessons = $lessonRepository->findBy(['chapter' => $chapter, 'user' => $this->getUser()]);

        if (!$lessons) {
            // if ($this->getUser()->getUserIdentifier() !== $chapter->getUser()->getEmail()) {

            //     $this->addFlash('warning', 'You cannot access or modify this chapter.');

            //     $this->redirectToRoute('app_courses_chapter_index', ['id' => $chapter->getId()]);
            // }

            $this->addFlash('error', 'No Lessons Have Been Created For '  . $chapter->getTitle() . ' Chapter. Kindly Create New');
        }

        return $this->render('courses/lesson/index.html.twig', [
            'lessons' => $lessons,
            'course_slug' => $course->getSlug(),
            'chapter_slug' => $chapter->getChapterslug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}/lesson/new', name: 'app_courses_lesson_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_new(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        EntityManagerInterface $entityManager
    ): Response {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $lesson->setUser($this->getUser());

            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_courses_lesson_index',
                [
                    'slug' => $course->getslug(),
                    'chapter_slug' => $chapter->getChapterslug()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'course_slug' => $course->getSlug(),
            'chapter_slug' => $chapter->getChapterslug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}/lesson/{lid}', name: 'app_courses_lesson_show', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'lid')] Lesson $lesson
    ): Response {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'course_slug' => $course->getSlug(),
            'chapter_slug' => $chapter->getChapterslug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}/lesson/{lid}/edit', name: 'app_courses_lesson_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'lid')] Lesson $lesson,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $lesson->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            return $this->redirectToRoute(
                'app_courses_lesson_index',
                [
                    'slug' => $course->getSlug(),
                    'chapter_slug' => $chapter->getChapterslug()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'course_slug' => $course->getSlug(),
            'chapter_slug' => $chapter->getChapterslug()
        ]);
    }

    #[Route('/{slug}/chapter/{chapterslug}/lesson/{lid}', name: 'app_courses_lesson_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_delete(
        Request $request,

        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'lid')] Lesson $lesson,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_courses_lesson_index',
            [
                'slug' => $course->getSlug(),
                'chapter_slug' => $chapter->getChapterslug()
            ],
            Response::HTTP_SEE_OTHER
        );
    }
    /*
    *
    * ************************************* End:: Lessons CRUD ****************************
    *
    */

    #[Route('/{slug}/chapter/{chapterslug}', name: 'app_courses_chapter_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_delete(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_chapter_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
    }
    /*
    *
    * ************************************* End:: Chapter CRUD ****************************
    *
    */

    /*
    *
    ************************************ Start:: Reviews CRUD ****************************
    *
    */
    #[Route('/{slug}/review', name: 'app_courses_reviews_index', methods: ['GET'])]
    public function courses_reviews_index(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        ReviewsRepository $reviewsRepository
    ): Response {
        $reviews = $reviewsRepository->findBy([
            'course' => $course
        ]);

        if (!$reviews) {
            $this->addFlash('error', 'No Reviews Were Found For '  . $course->getTitle());
        }

        return $this->render('courses/reviews/index.html.twig', [
            'reviews' => $reviews,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/review/new', name: 'app_courses_reviews_new', methods: ['GET', 'POST'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_new(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EntityManagerInterface $entityManager
    ): Response {
        $verifyReviewExistsForUser = $course->reviewExistsForUser($this->getUser());

        if ($verifyReviewExistsForUser) {
            $this->addFlash('error', 'Sorry! You Can Only Add One Review Per Enrolled Course. Kindly Modify Your Previous Review If Your Impression About The Course Has Changed. Thanks');

            return $this->redirectToRoute('app_courses_reviews_index', ['slug' => $course->getSlug()]);
        }

        $verifyUserIsEnrolled = $course->isUserEnrolled($this->getUser());

        if (!$verifyUserIsEnrolled) {
            $this->addFlash('warning', 'Sorry! You Can Only Add Reviews To Courses You Are Enrolled For. Thanks');

            return $this->redirectToRoute('app_courses_reviews_index', ['slug' => $course->getSlug()]);
        }

        $review = new Reviews();
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setName(ucwords($form->get('name')->getData()));

            $review->setCourse($course);

            $review->setUser($this->getUser());

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_reviews_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/reviews/new.html.twig', [
            'review' => $review,
            'form' => $form,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/review/{reviewslug}', name: 'app_courses_reviews_show', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review
    ): Response {
        return $this->render('courses/reviews/show.html.twig', [
            'review' => $review,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/review/{reviewslug}/edit', name: 'app_courses_reviews_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_reviews_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/reviews/edit.html.twig', [
            'review' => $review,
            'form' => $form,
            'course_slug' => $course->getSlug()
        ]);
    }

    #[Route('/{slug}/review/{reviewslug}', name: 'app_courses_reviews_delete', methods: ['POST'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_delete(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_reviews_index', ['slug' => $course->getSlug()], Response::HTTP_SEE_OTHER);
    }
    /*
    *
    * ************************************* End:: Reviews CRUD ****************************
    *
    */

    #[Route('/{slug}/enroll', name: 'app_courses_enroll', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_enroll(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EntityManagerInterface $entityManager
    ): Response {
        $enrollment = new Enrollment;
        $enrollment->setEnrolledDate(new \DateTimeImmutable());
        $enrollment->setUser($this->getUser());
        $enrollment->setCourses($course);

        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Congratulations! You have enrolled for '  . $course->getTitle());

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{slug}/unenroll', name: 'app_courses_unenroll', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_unenroll(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EnrollmentRepository $enrollmentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $enrollment = $enrollmentRepository->findOneBy(['courses' => $course]);

        if ($enrollment) {
            $entityManager->remove($enrollment);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully unenrolled ' . $enrollment->getUser()->getFirstname() . ' from '  . $course->getTitle());
        }

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{slug}/enrolled', name: 'app_courses_enrolled', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_enrolled(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EnrollmentRepository $enrollmentRepository
    ): Response {
        if ($this->getUser()->getUserIdentifier() !== $course->getUser()->getEmail()) {

            $this->addFlash('warning', 'You can only see students enrolled in your courses.');

            return $this->redirectToRoute('app_courses_index');
        }

        $enrollments = $enrollmentRepository->findBy(['courses' => $course]);

        if (!$enrollments) {
            $this->addFlash('error', 'No Enrollments Were Found For '  . $course->getTitle());
        }

        return $this->render('courses/enrolled.html.twig', [
            'enrollments' => $enrollments ?? [],
        ]);
    }

    #[Route('/{slug}/lecture', name: 'app_courses_lecture', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_learning_lecture(
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
    ): Response {
        return $this->render(
            'courses/lecture.html.twig',
            ['course' => $course]
        );
    }

    #[Route('/{slug}', name: 'app_courses_delete', methods: ['POST'], requirements: ['slug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_delete(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Courses $course,
        EntityManagerInterface $entityManager
    ): Response {
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

    protected function courses_last_accessed(Courses $course, CoursesRepository $coursesRepository, EntityManagerInterface $entityManager): Response
    {
        $course = $coursesRepository->findOneBy(['user' => $this->getUser(), 'courses' => $course]);

        $course->setLastAccessed(new \DateTimeImmutable());

        $entityManager->persist($course);
        $entityManager->flush();

        /**
         * Triggered pre logout action
         */

        return $this->redirectToRoute('app_courses_learning');
    }

    protected function courses_completed(Courses $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
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
