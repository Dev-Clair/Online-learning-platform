<?php

namespace App\Controller;

use App\Entity\Users\Admin;
use App\Entity\Users\Instructor;
use App\Entity\Users\Student;
use App\Event\UserAccountCreatedEvent;
use App\Form\AdminType;
use App\Form\InstructorType;
use App\Repository\CoursesRepository;
use App\Repository\AdminRepository;
use App\Repository\InstructorRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(
        private CacheInterface $redisCache,
        private EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(AdminRepository $adminRepository): Response
    {
        $admins = $adminRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/instructors', name: 'app_admin_instructors', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index_instructors(InstructorRepository $instructorRepository): Response
    {
        $admins = $instructorRepository->getInstructors();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/students', name: 'app_admin_students', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index_students(StudentRepository $studentRepository): Response
    {
        $admins = $studentRepository->getStudents();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/products', name: 'app_admin_product', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index_products(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/new-admin', name: 'app_admin_new_admin', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $admin = new Admin;
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $admin->setFirstname(ucwords($form->get('firstName')->getData()));

            $admin->setLastname(ucwords($form->get('lastName')->getData()));

            // Assign roles
            $defaultRole = ['ROLE_ADMIN'];

            $roles = filter_var($form->get('roles')->getData(), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($roles)) {
                $defaultRole[] =  $roles;
            }

            $admin->setRoles($defaultRole);

            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            // Dispatch Account Creation Notification Email To New Admin User
            $event = new UserAccountCreatedEvent($admin);
            $this->eventDispatcher->dispatch($event, UserAccountCreatedEvent::NAME);

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/new-instructor', name: 'app_admin_new_instructor', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new_instructor(Request $request): Response
    {
        $instructor = new Instructor;
        $form = $this->createForm(InstructorType::class, $instructor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $instructor->setFirstname(ucwords($form->get('firstName')->getData()));

            $instructor->setLastname(ucwords($form->get('lastName')->getData()));

            // Assign roles
            $defaultRole = ['ROLE_INSTRUCTOR'];

            $roles = filter_var($form->get('roles')->getData(), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($roles)) {
                $defaultRole[] =  $roles;
            }

            $instructor->setRoles($defaultRole);

            $this->entityManager->persist($instructor);
            $this->entityManager->flush();

            // Dispatch Account Creation Notification Email To New Instructor User
            $event = new UserAccountCreatedEvent($instructor);
            $this->eventDispatcher->dispatch($event, UserAccountCreatedEvent::NAME);

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $instructor,
            'form' => $form,
        ]);
    }

    #[Route('/{userslug}', name: 'app_admin_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Admin $admin,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Instructor $instructor,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Student $student
    ): Response {
        return $this->render('user/show.html.twig', [
            'user' => $admin ?? $instructor ?? $student,
        ]);
    }

    #[Route('/{userslug}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] Admin $admin
    ): Response {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $admin->setFirstname(ucwords($form->get('firstName')->getData()));

            $admin->setLastname(ucwords($form->get('lastName')->getData()));

            $admin->setEmail($form->get('email')->getData());

            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{userslug}', name: 'app_admin_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Admin $admin): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($admin);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
