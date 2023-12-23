<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Testimonial;
use App\Form\TestimonialFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create new testimonial instance 
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialFormType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Retrieve the email submitted by user
            $form_email = $form->get('email')->getData();
            // Check if a valid user with the email address exists in the database
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form_email]);

            if ($user) {

                // Create new testimonial
                $entityManager->persist($testimonial);
                $entityManager->flush();

                $this->addFlash('success', 'Thanks for helping us get better! Kindly subscribe to our newsletter to be in the loop about our future events and activities.');

                return $this->redirectToRoute('app_testimonial');
            }

            // Redirect with form errors if any
            $form->get('email')->addError(new FormError('No user found with this email address.'));
            return $this->redirectToRoute('app_testimonial');
        }

        // Fetch testimonials
        $testimonials = $entityManager->getRepository(Testimonial::class)->findAll() ?? [];

        return $this->render('testimonial/index.html.twig', [
            'title' => 'Testimonial',
            'testimonials' => $testimonials ?? "",
            'testimonialForm' => $form
        ]);
    }
}
