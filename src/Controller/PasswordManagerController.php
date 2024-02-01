<?php

namespace App\Controller;

use App\Repository\AdminRepository;
use App\Repository\InstructorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/password')]
class PasswordManagerController extends AbstractController
{
    #[Route('/', name: 'app_password_index', methods: ['GET'])]
    // #[IsGranted('PUBLIC_ACCESS')]
    public function password_index(): Response
    {
        return $this->render('password_manager/index.html.twig');
    }

    #[Route('/create', name: 'app_password_create', methods: ['POST'])]
    // #[IsGranted('PUBLIC_ACCESS')]
    public function password_create(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        AdminRepository $adminRepository,
        InstructorRepository $instructorRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Validate Email
        $email = filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL);

        if (empty($email)) {
            $this->addFlash('error', 'Invalid email. Please enter e valid email.');

            return $this->redirectToRoute('app_password_index');
        }

        // Validate Password
        $password = filter_var($request->request->get('password'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $confirm_password = filter_var($request->request->get('confirm_password'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($password !== $confirm_password) {
            $this->addFlash('error', 'Password does not match. Please try again.');

            return $this->redirectToRoute('app_password_index');
        }

        // Retrieve User Account via Email
        $user = $adminRepository->findOneBy(['email' => $email]) ?? $instructorRepository->findOneBy(['email' => $email]);

        if ((bool) $user) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password created successfully. Log in to contine.');
        }

        return $this->redirectToRoute('app_login');
    }
}
