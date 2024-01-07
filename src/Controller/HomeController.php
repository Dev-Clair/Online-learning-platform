<?php

namespace App\Controller;

use App\Repository\TestimonialRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(UserRepository $userRepository, TestimonialRepository $testimonialRepository, CacheInterface $redisCache): Response
    {
        // $app_home_cache = $redisCache->get('app_home', function (ItemInterface $item) use ($userRepository, $testimonialRepository): array {
        //     $item->expiresAfter(1800);

        //     return [

        //         'instructors' => $userRepository->findAll() ?? [],

        //         'testimonials' => $testimonialRepository->findBy([], [], limit: 2, offset: 0) ?? []
        //     ];
        // });


        $app_home_cache = [
            'instructors' => array_slice($userRepository->getInstructors(), 0, 3) ?? [],

            'testimonials' => $testimonialRepository->findBy([], [], limit: 2, offset: 0) ?? []
        ];

        return $this->render('home/index.html.twig', $app_home_cache);
    }
}
