<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class ZDefaultController extends AbstractController
{
    #[Route(path: '{catchall}', name: 'app_home', requirements: ['catchall' => '.+'])]
    public function Default(): Response
    {
        return $this->redirectToRoute('app_home_index');
    }
}