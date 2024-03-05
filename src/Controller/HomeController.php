<?php

namespace App\Controller;

use App\Entity\Users\Instructor;
use App\Repository\TestimonialRepository;
use App\Repository\InstructorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(InstructorRepository $instructorRepository, TestimonialRepository $testimonialRepository, CacheInterface $cache): Response
    {
        // $app_home_cache = $cache->get('app_home', function (ItemInterface $item) use ($instructorRepository, $testimonialRepository): array {
        //     $item->expiresAfter(1800);

        //     return [

        //         'instructors' => $instructorRepository->findAll() ?? [],

        //         'testimonials' => $testimonialRepository->findBy([], [], limit: 2, offset: 0) ?? []
        //     ];
        // });


        $app_home_cache = [
            'instructors' => $instructorRepository->getInstructors() ?? [],

            // 'testimonials' => $testimonialRepository->findBy([], [], limit: 2, offset: 0) ?? []
        ];

        return $this->render('home/index.html.twig', $app_home_cache);
    }
}
