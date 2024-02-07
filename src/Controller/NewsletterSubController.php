<?php

namespace App\Controller;

use App\Entity\NewsletterSub;
use App\Form\NewsletterSubType;
use App\Repository\NewsletterSubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/newsletter/sub')]
class NewsletterSubController extends AbstractController
{
    #[Route('/', name: 'app_newsletter_sub_index', methods: ['GET'])]
    #[IsGranted('ROLE_CUSTOMER_CARE')]
    public function index(NewsletterSubRepository $newsletterSubRepository): Response
    {
        return $this->render('newsletter_sub/index.html.twig', [
            'newsletter_subs' => $newsletterSubRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_newsletter_sub_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');

        $newsletterSub = new NewsletterSub();
        $newsletterSub->setEmail($email);

        $entityManager->persist($newsletterSub);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{id}', name: 'app_newsletter_sub_delete', methods: ['POST'])]
    public function delete(Request $request, NewsletterSub $newsletterSub, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $newsletterSub->getId(), $request->request->get('_token'))) {
            $entityManager->remove($newsletterSub);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_newsletter_sub_index', [], Response::HTTP_SEE_OTHER);
    }
}
