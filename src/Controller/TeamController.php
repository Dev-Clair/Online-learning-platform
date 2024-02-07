<?php

namespace App\Controller;

use App\Repository\InstructorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/team', name: 'app_team')]
    public function index(InstructorRepository $instructorRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'instructors' => $instructorRepository->getInstructors() ?? []
        ]);
    }
}
