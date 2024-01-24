<?php

namespace App\Controller;

use App\Entity\Users\Admin;
use App\Entity\Users\Instructor;
use App\Entity\Users\Student;
use App\Form\PasswordManagerType;
use App\Repository\AdminRepository;
use App\Repository\InstructorRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/password')]
class PasswordManagerController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private AdminRepository $adminRepository,
        private InstructorRepository $instructorRepository,
        private StudentRepository $studentRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/create/{userslug}', name: 'app_password_create', methods: ['GET'])]
    // #[IsGranted('PUBLIC_ACCESS')]
    public function password_create(
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Admin $admin = null,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Instructor $instructor = null,
        #[MapEntity(mapping: ['userslug' => 'userslug'])] ?Student $student = null,
        Request $request
    ): Response {
        $user = $admin ?? $instructor ?? $student;
        $form = $this->createForm(PasswordManagerType::class,  $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();

            $password = $form->get('password')->getData();

            $confirm_password = $form->get('confirm_password')->getData();

            if ($password !== $confirm_password) {
                $this->addFlash('error', 'Password Does Nor Match. Please Try Again.');

                return $this->redirectToRoute('app_password_create', ['userslug' => $user->getUserSlug()]);
            }

            $verifyEmail = (bool) $this->adminRepository->findOneBy(['email' => $email])
                ??
                (bool) $this->instructorRepository->findOneBy(['email' => $email])
                ??
                (bool) $this->studentRepository->findOneBy(['email' => $email]);

            if ($verifyEmail === true) {
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )
                );

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Password Created Successfully. Kindly Log in.');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'No User Account Exists For This email' . $email);

            return $this->redirectToRoute('app_password_create', ['userslug' => $user->getUserSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('password_manager/index.html.twig', []);
    }
}
