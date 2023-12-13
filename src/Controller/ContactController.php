<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        $contact = [];

        return $this->render('contact/index.html.twig', [
            'title' => 'Contact',
            'contact' => $contact ?? "",
        ]);
    }

    #[Route('/contact', name: 'app_inquiry')]
    public function inquiry(): Response
    {
        $inquiry_error = false;

        $inquiry_success = true;

        return $this->render('contact/index.html.twig', [
            'title' => 'Contact',
            'inquiry_response' => $inquiry_success ?? $inquiry_error
        ]);
    }
}
