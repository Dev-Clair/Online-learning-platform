<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial')]
    public function index(): Response
    {
        $testimonials = [];

        return $this->render('testimonial/index.html.twig', [
            'title' => 'Testimonials',
            'testimonials' => $testimonials ?? "",
        ]);
    }
}
