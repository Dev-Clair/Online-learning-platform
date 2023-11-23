<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $courses = ['web design', 'ui/ux', 'graphics design'];
        $instructors = ['hennadi shvedko', 'elina chifeac', 'raul adrian'];
        $testimonials = ['Great academy', 'Great Instructors', 'Great support'];

        return $this->render('home/index.html.twig', [
            'title' => 'Home',
            'courses' => $courses ?? "",
            'instructors' => $instructors ?? "",
            'testimonials' => $testimonials ?? "",
        ]);
    }
}
