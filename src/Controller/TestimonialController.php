<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\Testimonial;
use App\Form\TestimonialFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Create new testimonial instance 
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialFormType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Retrieve the email submitted by user
            $form_email = $form->get('email')->getData();
            // Check if a valid user with the email address exists in the database
            $user = $userRepository->findOneBy(['email' => $form_email]);

            if ($user) {

                // Create new testimonial
                $entityManager->persist($testimonial);
                $entityManager->flush();

                $this->addFlash('success', 'Thanks for helping us get better! Kindly subscribe to our newsletter to be in the loop about our future events and activities.');

                return $this->redirectToRoute('app_testimonial');
            }

            return $this->redirectToRoute('app_testimonial');
        }

        // Fetch testimonials
        $testimonials = $entityManager->getRepository(Testimonial::class)->findAll() ?? [];

        return $this->render('testimonial/index.html.twig', [
            'testimonials' => $testimonials ?? [],
            'testimonialForm' => $form
        ]);
    }
}
