<?php

namespace App\Controller;

use App\Repository\CoursesRepository;
use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(CoursesRepository $coursesRepository, TestimonialRepository $testimonialRepository, CacheInterface $cache): Response
    {
        $homeController = $cache->get('app_home', function (ItemInterface $item) use ($coursesRepository, $testimonialRepository): array {
            $item->expiresAfter(1800);

            return [
                'courses' => $coursesRepository->findBy([], [], limit: 2, offset: 0) ?? [],

                'testimonials' => $testimonialRepository->findBy([], [], limit: 2, offset: 0) ?? []
            ];
        });

        return $this->render('home/index.html.twig', $homeController);
    }
}
