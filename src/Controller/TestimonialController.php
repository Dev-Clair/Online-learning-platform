<?php

namespace App\Controller;

use App\Entity\Testimonial;
use App\Form\TestimonialType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/review', name: 'app_testimonial_form')]
    public function create(): Response
    {
        $testimonials = [];

        return $this->render('testimonial/create.html.twig', ['title' => 'Reviews']);
    }

    #[Route('/review', name: 'app_testimonial_create')]
    public function store(): Response
    {
        $testimonials = [];

        // return $this->render('testimonial/index.html.twig', [
        //     'title' => 'Testimonials',
        //     'testimonials' => $testimonials ?? "",
        // ]);
    }
}
