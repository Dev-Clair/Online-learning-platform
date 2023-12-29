<?php

namespace App\Controller;

use App\Repository\CoursesRepository;
use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(CoursesRepository $coursesRepository, TestimonialRepository $testimonialRepository): Response
    {
        $courses = $coursesRepository->findAll() ?? [];

        $testimonials = $testimonialRepository->findAll() ?? [];

        return $this->render('home/index.html.twig', [
            'courses' => $courses,
            'testimonials' => $testimonials
        ]);
    }
}
