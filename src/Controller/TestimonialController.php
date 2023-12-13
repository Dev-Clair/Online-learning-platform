<?php

namespace App\Controller;

use App\Entity\User;
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
            'title' => 'Testimonial',
            'testimonials' => $testimonials ?? "",
        ]);
    }

    #[Route('/review', name: 'app_review')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $form_user = $form->get('users')->getData();
            $form_email = $form->get('email')->getData();

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form_email]);

            $form_name = $form->get('name')->getData();
            $form_testimonial = $form->get('testimonial')->getData();

            $testimonial->setName($form_name);
            $testimonial->setEmail($form_email);
            $testimonial->setTestimonial($form_testimonial);
            $testimonial->setUser($user);

            $entityManager->persist($testimonial);
            $entityManager->flush();

            return $this->redirectToRoute('app_testimonial');
        }

        return $this->render('testimonial/create.html.twig', [
            'title' => 'Testimonial',
            'testimonialForm' => $form->createView(),
        ]);
    }
}
