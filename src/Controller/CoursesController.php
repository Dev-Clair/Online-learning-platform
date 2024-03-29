<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Courses;
use App\Entity\Enrollment;
use App\Entity\Lesson;
use App\Entity\Reviews;
use App\Entity\Users\Instructor;
use App\Event\CourseEnrollmentEvent;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
class CoursesController extends AbstractController
{
    public function __construct(
        private CacheInterface $cache,
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_courses_index', methods: ['GET'])]
    public function courses_index(CoursesRepository $coursesRepository): Response
    {
        if ($this->getUser() instanceof Instructor) {
            return $this->render('courses/index.html.twig', [
                'courses' => $coursesRepository->findBy(['instructor' => $this->getUser()]),
            ]);
        }

        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_courses_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_new(Request $request): Response
    {
        $course = new Courses();
        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $course->setTitle(ucwords($form->get('title')->getData()));

            $course->setDescription(ucwords($form->get('description')->getData()));

            $course->setInstructor($this->getUser());

            $this->entityManager->persist($course);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_courses_index');
        }

        return $this->render('courses/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/content', name: 'app_courses_content', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_content(CoursesRepository $coursesRepository): Response
    {
        $courses = $coursesRepository->findBy(['instructor' => $this->getUser()]);

        if (!$courses) {
            $this->addFlash('error', 'No Content Have Been Created For this Course. Kindly Create New');
        }

        return $this->render('courses/content.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/learning', name: 'app_courses_learning', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_learning(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findBy(['student' => $this->getUser()], ['dateEnrolled' => 'ASC']);

        if (!$enrollments) {
            $this->addFlash('error', 'You Have Not Enrolled For Any Courses. Click On Courses To See Available Courses.');
        }

        return $this->render('courses/learning.html.twig', [
            'enrollments' => $enrollments
        ]);
    }

    #[Route('/{courseslug}', name: 'app_courses_show', methods: ['GET'], requirements: ['slug' => '[^/]+'])]
    public function courses_show(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{courseslug}/edit', name: 'app_courses_edit', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_edit(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        if ($this->getUser()->getUserIdentifier() !== $course->getInstructor()->getEmail()) {

            $this->addFlash('warning', 'You Are Not Authorized To Edit This Course.');

            return $this->redirectToRoute('app_courses_index');
        }

        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

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
    #[Route('/{courseslug}/chapter', name: 'app_courses_chapter_index', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_index(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        ChapterRepository $chapterRepository
    ): Response {
        $chapters = $chapterRepository->findBy(['courses' => $course, 'instructor' => $this->getUser()]);

        if (!$chapters) {
            if ($this->getUser()->getUserIdentifier() !== $course->getInstructor()->getEmail()) {

                $this->addFlash('warning', 'You Cannot Access Or Modify This Course.');

                return $this->redirectToRoute('app_courses_index');
            }

            $this->addFlash('error', 'No Chapters Have Been Created For '  . $course->getTitle() . ' Course. Kindly Create New');
        }

        return $this->render('courses/chapter/index.html.twig', [
            'chapters' => $chapters,
            'course_slug' => $course->getCourseSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/new', name: 'app_courses_chapter_new', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_new(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $chapter->setTitle(ucwords($form->get('title')->getData()));

            $chapter->setCourses($course);

            $chapter->setInstructor($this->getUser());

            $this->entityManager->persist($chapter);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
            'course_slug' => $course->getCourseSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}', name: 'app_courses_chapter_show', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_show(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter
    ): Response {
        return $this->render('courses/chapter/show.html.twig', [
            'chapter' => $chapter,
            'course_slug' => $course->getCourseSlug(),
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}/edit', name: 'app_courses_chapter_edit', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_edit(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter
    ): Response {
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_courses_chapter_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
            'course_slug' => $course->getCourseSlug(),
        ]);
    }

    /*
    *
    * ************************************* Start:: Lessons CRUD ****************************
    *
    */
    #[Route('/{courseslug}/chapter/{chapterslug}/lesson', name: 'app_courses_lesson_index', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_index(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        LessonRepository $lessonRepository
    ): Response {

        $lessons = $lessonRepository->findBy(['chapter' => $chapter, 'instructor' => $this->getUser()]);

        if (!$lessons) {
            $this->addFlash('error', 'No Lessons Have Been Created For '  . $chapter->getTitle() . ' Chapter. Kindly Create New');
        }

        return $this->render('courses/lesson/index.html.twig', [
            'lessons' => $lessons,
            'course_slug' => $course->getCourseSlug(),
            'chapter_slug' => $chapter->getChapterSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}/lesson/new', name: 'app_courses_lesson_new', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_new(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter
    ): Response {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $lesson->setChapter($chapter);

            $lesson->setInstructor($this->getUser());

            $this->entityManager->persist($lesson);
            $this->entityManager->flush();

            return $this->redirectToRoute(
                'app_courses_lesson_index',
                [
                    'courseslug' => $course->getCourseSlug(),
                    'chapterslug' => $chapter->getChapterSlug()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('courses/lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'course_slug' => $course->getCourseSlug(),
            'chapter_slug' => $chapter->getChapterSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}/lesson/{id}', name: 'app_courses_lesson_show', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_show(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'id')] Lesson $lesson
    ): Response {
        return $this->render('courses/lesson/show.html.twig', [
            'lesson' => $lesson,
            'course_slug' => $course->getCourseSlug(),
            'chapter_slug' => $chapter->getChapterSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}/lesson/{id}/edit', name: 'app_courses_lesson_edit', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_edit(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'id')] Lesson $lesson
    ): Response {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute(
                'app_courses_lesson_index',
                [
                    'courseslug' => $course->getCourseSlug(),
                    'chapterslug' => $chapter->getChapterSlug()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('courses/lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'course_slug' => $course->getCourseSlug(),
            'chapter_slug' => $chapter->getChapterSlug()
        ]);
    }

    #[Route('/{courseslug}/chapter/{chapterslug}/lesson/{id}', name: 'app_courses_lesson_delete', methods: ['POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_lesson_delete(
        Request $request,

        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter,
        #[MapEntity(id: 'id')] Lesson $lesson
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($lesson);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_courses_lesson_index',
            [
                'courseslug' => $course->getCourseSlug(),
                'chapterslug' => $chapter->getChapterSlug()
            ],
            Response::HTTP_SEE_OTHER
        );
    }
    /*
    *
    * ************************************* End:: Lessons CRUD ****************************
    *
    */

    #[Route('/{courseslug}/chapter/{chapterslug}', name: 'app_courses_chapter_delete', methods: ['POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_chapter_delete(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['chapterslug' => 'chapterslug'])] Chapter $chapter
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $chapter->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($chapter);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_chapter_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
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
    #[Route('/{courseslug}/review', name: 'app_courses_reviews_index', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    public function courses_reviews_index(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
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
            'course_slug' => $course->getCourseSlug()
        ]);
    }

    #[Route('/{courseslug}/review/new', name: 'app_courses_reviews_new', methods: ['GET', 'POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_new(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        $verifyReviewExistsForStudent = $course->reviewExistsForStudent($this->getUser());

        if ($verifyReviewExistsForStudent) {
            $this->addFlash('error', 'Sorry! You Can Only Add One Review Per Enrolled Course. Kindly Modify Your Previous Review If Your Impression About The Course Has Changed. Thanks');

            return $this->redirectToRoute('app_courses_reviews_index', ['courseslug' => $course->getCourseSlug()]);
        }

        $verifyStudentIsEnrolled = $course->isStudentEnrolled($this->getUser());

        if (!$verifyStudentIsEnrolled) {
            $this->addFlash('warning', 'Sorry! You Can Only Add Reviews To Courses You Are Enrolled In. Kindly Enroll A COurse And Try Again. Thanks');

            return $this->redirectToRoute('app_courses_index');
        }

        $review = new Reviews();
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setName(ucwords($form->get('name')->getData()));

            $review->setCourse($course);

            $review->setStudent($this->getUser());

            $this->entityManager->persist($review);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_courses_reviews_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/reviews/new.html.twig', [
            'review' => $review,
            'form' => $form,
            'course_slug' => $course->getCourseSlug()
        ]);
    }

    #[Route('/{courseslug}/review/{reviewslug}', name: 'app_courses_reviews_show', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_show(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review
    ): Response {
        return $this->render('courses/reviews/show.html.twig', [
            'review' => $review,
            'course_slug' => $course->getcourseSlug()
        ]);
    }

    #[Route('/{courseslug}/review/{reviewslug}/edit', name: 'app_courses_reviews_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_edit(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review
    ): Response {
        $form = $this->createForm(ReviewsType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_courses_reviews_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/reviews/edit.html.twig', [
            'review' => $review,
            'form' => $form,
            'course_slug' => $course->getCourseSlug()
        ]);
    }

    #[Route('/{courseslug}/review/{reviewslug}', name: 'app_courses_reviews_delete', methods: ['POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_reviews_delete(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        #[MapEntity(mapping: ['reviewslug' => 'reviewslug'])] Reviews $review
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_reviews_index', ['courseslug' => $course->getCourseSlug()], Response::HTTP_SEE_OTHER);
    }
    /*
    *
    * ************************************* End:: Reviews CRUD ****************************
    *
    */

    #[Route('/{courseslug}/enroll', name: 'app_courses_enroll', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_enroll(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        $enrollment = new Enrollment;
        $enrollment->setDateEnrolled(new \DateTimeImmutable());
        $enrollment->setStudent($this->getUser());
        $enrollment->setCourses($course);

        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Congratulations! You Have Enrolled For '  . $course->getTitle());

        // Dispatch Enrollment Notification Email To Student
        $event = new CourseEnrollmentEvent($enrollment);
        $this->eventDispatcher->dispatch($event, CourseEnrollmentEvent::ENROLLMENT);

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{courseslug}/unenroll', name: 'app_courses_unenroll', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_unenroll(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        EnrollmentRepository $enrollmentRepository
    ): Response {
        $enrollment = $enrollmentRepository->findOneBy(['courses' => $course]);

        if ($enrollment) {
            $this->entityManager->remove($enrollment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully Unenrolled ' . $enrollment->getStudent()->getFirstname() . ' From '  . $course->getTitle());
        }

        // Dispatch Un-Enrollment Notification Email To Student
        $event = new CourseEnrollmentEvent($enrollment);
        $this->eventDispatcher->dispatch($event, CourseEnrollmentEvent::UNENROLLMENT);

        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{courseslug}/enrolled', name: 'app_courses_enrolled', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_enrolled(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
        EnrollmentRepository $enrollmentRepository
    ): Response {
        if ($this->getUser()->getUserIdentifier() !== $course->getInstructor()->getEmail()) {

            $this->addFlash('warning', 'You Can Only See Students Enrolled In Your Course.');

            return $this->redirectToRoute('app_courses_index');
        }

        $enrollments = $enrollmentRepository->findBy(['courses' => $course]);

        if (!$enrollments) {
            $this->addFlash('error', 'No Enrollments Were Found For '  . $course->getTitle());
        }

        return $this->render('courses/enrolled.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }

    #[Route('/{courseslug}/learning/lesson', name: 'app_courses_learning_lesson', methods: ['GET'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_STUDENT')]
    public function courses_learning_lesson(
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course,
    ): Response {
        return $this->render(
            'courses/lesson.html.twig',
            ['course' => $course]
        );
    }

    #[Route('/{courseslug}', name: 'app_courses_delete', methods: ['POST'], requirements: ['courseslug' => '[^/]+'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function courses_delete(
        Request $request,
        #[MapEntity(mapping: ['courseslug' => 'courseslug'])] Courses $course
    ): Response {
        if ($this->getUser()->getUserIdentifier() !== $course->getInstructor()->getEmail()) {

            $this->addFlash('warning', 'You Are Not Authorized To Delete This Course.');

            return $this->redirectToRoute('app_courses_index');
        }

        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($course);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_index');
    }

    /**
     * 
     * Triggered on pre-logout event or session-expiry
     * 
     */
    protected function OnLogOut(Enrollment $enrollment): void
    {
        $enrollment->setLastAccessed(new \DateTimeImmutable());

        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();
    }

    /**
     * 
     * Triggered on course completion >>> Triggers course completion mail event
     * 
     */
    protected function OnCourseCompletion(Enrollment $enrollment): void
    {
        $enrollment->setDateCompleted(new \DateTimeImmutable());

        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();
    }

    /**
     * Triggered on lesson completion
     * 
     */
    protected function OnLessonCompleted(Lesson $lesson): void
    {
        // $lesson->setStatus('completed');

        // $this->entityManager->persist($lesson);
        // $this->entityManager->flush();

        return;
    }
}
