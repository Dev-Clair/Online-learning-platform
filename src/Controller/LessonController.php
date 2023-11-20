<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonController extends AbstractController
{
    #[Route('/lesson', name: 'app_lesson')]
    public function index(): Response
    {
        $lessons = [];

        return $this->render('lesson/index.html.twig', [
            'title' => 'Lessons',
            'lessons' => $lessons,
        ]);
    }
}
