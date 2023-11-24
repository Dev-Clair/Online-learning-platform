<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'title' => 'Users',
            'user' => $user,
        ]);
    }
}
