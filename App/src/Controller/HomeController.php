<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home_index')]
    public function index(): Response
    {
        $model = 'Hello World';
        $cesta = 'Account/main.html';
       return $this->render('main.html.twig', [
           'pathToMain' => $cesta,
           'number' => $model
        ]);
    }

    #[Route(path: '/account', name: 'app_home')]
    public function home(): Response
    {
        $model = 'Home';
        return new Response($model);
    }

    #[Route(path: '/home/kok', name: 'app_home')]
    public function kok(): Response
    {
        $model = 'Home';
        return new Response($model);
    }
}