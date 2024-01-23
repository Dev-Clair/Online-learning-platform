<?php

namespace App\Controller;

use App\Entity\Users\Student;
use App\Event\UserAccountCreatedEvent;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $user = new Student();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setFirstname(ucwords($form->get('firstName')->getData()));

            $user->setLastname(ucwords($form->get('lastName')->getData()));

            $user->setRoles(["ROLE_STUDENT"]);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account Created Successfully, You can now Log in.');

            // Dispatch Account Creation Notification Email To New Student
            $event = new UserAccountCreatedEvent($user);
            $eventDispatcher->dispatch($event, UserAccountCreatedEvent::STUDENT);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
