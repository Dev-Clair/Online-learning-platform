<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create new contact instance 
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set status to zero
            $default_status = 0;
            $contact->setStatus($default_status);

            // Create new contact
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Thanks for reaching out! Our correspondent will be in touch shortly if you have submitted a question.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', ['contactForm' => $form]);
    }

    #[Route('/support', name: 'app_contact_support', methods: ['GET'])]
    #[IsGranted('ROLE_CUSTOMER_CARE')]
    public function support(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/support.html.twig', [
            'contacts' => $contactRepository->findAll()
        ]);
    }

    #[Route('/care/{id}/edit', name: 'app_contact_care', methods: ['GET'])]
    public function customercare(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CUSTOMER_CARE')) {
            // Update status
            $contact->setStatus(1);

            // Update time
            $contact->setResolvedAt(new DateTimeImmutable());

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_support');
        }

        return $this->redirectToRoute('app_contact_support', [], 401);
    }
}
