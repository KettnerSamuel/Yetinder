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

    #[Route(path: '/yetinder', name: 'app_Yetinder_index')]
    public function Yetinder(): Response
    {
        $cesta = 'YetinderPage/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }
    #[Route(path: '/yetinder/add', name: 'app_Form')]
    public function Form(): Response
    {
        if ($this->getUser()) {
            $cesta = 'Form/main.html';
            return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
        } else  {
            return $this->redirectToRoute('app_register');
        }

    }
}