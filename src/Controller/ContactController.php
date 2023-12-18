<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create new contact instance 
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Retrieve values from form fields
            $form_name = ucwords($form->get('name')->getData());
            $form_email = $form->get('email')->getData();
            $form_subject = $form->get('subject')->getData();
            $form_message = $form->get('message')->getData();

            // Assign form values to entity properties
            $contact->setName($form_name);
            $contact->setEmail($form_email);
            $contact->setSubject($form_subject);
            $contact->setMessage($form_message);

            // Set status to zero
            $default_status = 0;
            $contact->setStatus($default_status);

            // Create new contact
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'title' => 'Contact',
            'contactForm' => $form->createView()
        ]);
    }
}
