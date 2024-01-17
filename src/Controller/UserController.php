<?php

namespace App\Controller;

use app\Entity\Users\Admin;
use App\Entity\Users\Instructor;
use App\Entity\Users\Student;
use App\Entity\Users\User;
use App\Form\UserType;
use App\Repository\CoursesRepository;
use App\Repository\AdminRepository;
use App\Repository\InstructorRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(AdminRepository $adminRepository): Response
    {
        $users = $adminRepository->getGlobal();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/instructors', name: 'app_admin_instructors', methods: ['GET'])]
    public function index_instructors(InstructorRepository $adminRepository): Response
    {
        $users = $adminRepository->getInstructors();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/students', name: 'app_admin_students', methods: ['GET'])]
    public function index_students(StudentRepository $studentRepository): Response
    {
        $users = $studentRepository->getStudents();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/products', name: 'app_admin_user_product', methods: ['GET'])]
    public function index_products(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_instructor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Instructor;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setFirstname(ucwords($form->get('firstName')->getData()));

            $user->setLastname(ucwords($form->get('lastName')->getData()));

            $user->setEmail($form->get('email')->getData());

            $user->setRoles($form->get('roles')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // Send email to instructor with link/token to create password
            /**
             *  Require Mailer
             */

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the values from the form and Assign them to the user entity
            $user->setFirstname(ucwords($form->get('firstName')->getData()));

            $user->setLastname(ucwords($form->get('lastName')->getData()));

            $user->setEmail($form->get('email')->getData());

            $user->setRoles($form->get('roles')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
