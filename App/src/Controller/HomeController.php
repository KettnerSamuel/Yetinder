<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Yetties;
use App\Form\YetiFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route(path: '/yetinder', name: 'app_yetinder_index')]
    public function Yetinder(): Response
    {
        $cesta = 'YetinderPage/main.html';
        return $this->render('default.html.twig', [
           'pathToMain' => $cesta
        ]);
    }

    #[Route(path: '/yetinder/add', name: 'app_Form')]
    public function Form(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            if ($user) {
                $yeti = new Yetties();
                $form = $this->createForm(YetiFormType::class, $yeti);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $yeti->setDate(new \DateTime());
                    $yeti->setRating($user->getUsername());
                    $entityManager->persist($yeti);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_yetinder_index');
                }

                $cesta = 'Form/main.html';
                return $this->render('default.html.twig', [
                'pathToMain' => $cesta,
                'yetiForm' => $form
            ]);
            } else  {
                return $this->redirectToRoute('app_register');
            }
        }

    }
}