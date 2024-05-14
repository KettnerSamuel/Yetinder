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
        $cesta = 'Home/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }

    #[Route(path: '/account', name: 'app_account_index')]
    public function home(): Response
    {
        return $this->render('default.html.twig', [
            'pathToMain' => 'Account/main.html'
        ]);
    }

    #[Route(path: '/yetinder', name: 'app_Yetinder_index')]
    public function Yetinder(): Response
    {
        $cesta = 'YetinderPage/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }
    #[Route(path: '/yetinder/add', name: 'app_Form', methods: ['POST'])]
    public function Form(): Response
    {
        $cesta = 'Form/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }

    #[Route(path: '{catchall}', name: 'app_home', requirements: ['catchall' => '.+'])]
    public function Default(): Response
    {
        $cesta = 'Home/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }
}