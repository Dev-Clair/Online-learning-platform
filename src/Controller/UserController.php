<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CoursesRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->getGlobal();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    // #[Route('/team', name: 'app_admin_user_team', methods: ['GET'])]
    // public function index_team(UserRepository $userRepository): Response
    // {
    //     $users = $userRepository->getTeam();

    //     return $this->render('user/index.html.twig', [
    //         'users' => $users,
    //     ]);
    // }

    #[Route('/instructors', name: 'app_admin_user_instructors', methods: ['GET'])]
    public function index_instructors(UserRepository $userRepository): Response
    {
        $users = $userRepository->getInstructors();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/students', name: 'app_admin_user_students', methods: ['GET'])]
    public function index_students(UserRepository $userRepository): Response
    {
        $users = $userRepository->getStudents();

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

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setFirstname(ucwords($form->get('firstName')->getData()));

            $user->setLastname(ucwords($form->get('lastName')->getData()));

            $user->setEmail($form->get('email')->getData());

            $user->setRoles($form->get('roles')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // Send email to user with link/token to create password
            /**
             *  Require Mailer
             */

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
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

            $user->setUpdatedAt(new DateTimeImmutable());

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
