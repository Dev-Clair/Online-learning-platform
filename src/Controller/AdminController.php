<?php

namespace App\Controller;

use App\Entity\Users\Admin;
use App\Entity\Users\Instructor;
use App\Entity\Users\Student;
use App\Form\AdminType;
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

#[Route('/admin')]
// #[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(AdminRepository $adminRepository): Response
    {
        $admins = $adminRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/instructors', name: 'app_admin_instructors', methods: ['GET'])]
    public function index_instructors(InstructorRepository $instructorRepository): Response
    {
        $admins = $instructorRepository->getInstructors();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/students', name: 'app_admin_students', methods: ['GET'])]
    public function index_students(StudentRepository $studentRepository): Response
    {
        $admins = $studentRepository->getStudents();

        return $this->render('user/index.html.twig', [
            'users' => $admins,
        ]);
    }

    #[Route('/products', name: 'app_admin_product', methods: ['GET'])]
    public function index_products(CoursesRepository $coursesRepository): Response
    {
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $admin = new Admin;
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $admin->setFirstname(ucwords($form->get('firstName')->getData()));

            $admin->setLastname(ucwords($form->get('lastName')->getData()));

            $admin->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($admin);
            $entityManager->flush();

            // Send email to instructor with link/token to create password
            /**
             *  Require Mailer
             */

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{userslug}', name: 'app_admin_show', methods: ['GET'])]
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
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Admin $admin,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Instructor $instructor,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Student $student,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $admin ?? $instructor ?? $student;

        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setFirstname(ucwords($form->get('firstName')->getData()));

            $user->setLastname(ucwords($form->get('lastName')->getData()));

            $user->setEmail($form->get('email')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{userslug}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Admin $admin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
            $entityManager->remove($admin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
