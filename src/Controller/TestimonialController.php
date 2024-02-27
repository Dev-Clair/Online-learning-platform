<?php

namespace App\Controller;

use App\Entity\Testimonial;
use App\Form\TestimonialFormType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial', methods: ['GET', 'POST'])]
    public function index(Request $request, StudentRepository $studentRepository, EntityManagerInterface $entityManager): Response
    {
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialFormType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $form_email = $form->get('email')->getData();

            $user = $studentRepository->findOneBy(['email' => $form_email]);

            if ($user) {

                $entityManager->persist($testimonial);
                $entityManager->flush();

                $this->addFlash('success', 'Thanks for helping us get better!');

                return $this->redirectToRoute('app_testimonial');
            }

            return $this->redirectToRoute('app_testimonial');
        }

        // Fetch testimonials
        $testimonials = $entityManager->getRepository(Testimonial::class)->findAll() ?? [];

        return $this->render('testimonial/index.html.twig', [
            'testimonials' => $testimonials,
            'testimonialForm' => $form
        ]);
    }
}
