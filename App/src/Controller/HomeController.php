<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Yetties;
use App\Form\YetiFormType;
use App\Repository\YettiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Finder\Finder;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home_index')]
    public function index(YettiesRepository $yetiRepository): Response
    {
        $cesta = 'Home/main.html';
        $topTenYetis = $yetiRepository->findTopTenYetis();

        return $this->render('default.html.twig', [
           'pathToMain' => $cesta,
           'top_ten_yetis' => $topTenYetis
        ]);
    }

    #[Route(path: '/yetinder', name: 'app_yetinder_index')]
    public function Yetinder(): Response
    {
        if ($this->getUser()) {
            $cesta = 'YetinderPage/main.html';
            return $this->render('default.html.twig', [
               'pathToMain' => $cesta,
            ]);
        } else {
            return $this->redirectToRoute('app_register');
        }
    }

    #[Route(path: '/yetinder/add', name: 'app_Form')]
    public function Form(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Filesystem $filesystem, YettiesRepository $yetiRepository): Response
    {
        $error = null;
        $user = $this->getUser();
        if ($user instanceof User) {
            if ($user) {
                //new instances
                $finder = new Finder();
                $yeti = new Yetties();
                $form = $this->createForm(YetiFormType::class, $yeti);
                $form->handleRequest($request);
                //for image saving
                $directoryPath = '/var/www/html/public/uploads/images';
                $maxDirectorySize = 1024 * 1024 * 10; // 10 MB limit
                $repositoryGotSpace = null;

                // Ensure the directory exists
                if (!$filesystem->exists($directoryPath)) {
                    try {
                        $filesystem->mkdir($directoryPath, 0755);
                        chown('/var/www/html/public/uploads', 'www-data');
                        chgrp('/var/www/html/public/uploads', 'www-data');
                        chown($directoryPath, 'www-data');
                        chgrp($directoryPath, 'www-data');
                    } catch (\Exception $e) {
                        $repositoryGotSpace = false;
                    }
                }

                // Calculate directory size to avoid exceeding the limit
                if($repositoryGotSpace == false){
                    $directorySize = 0;
                    $finder->files()->in($directoryPath);
                    foreach ($finder as $file) {
                        $directorySize += $file->getSize();
                    }
                    //if
                    if ($directorySize < $maxDirectorySize) {
                        $repositoryGotSpace = true;
                    } else {
                        $repositoryGotSpace = false;
                    }
                }

                if (!$yetiRepository->findOneByName($yeti->getName())) {
                     if ($form->isSubmitted() && $form->isValid()) {
                        $yeti->setDate(new \DateTime());
                        $yeti->setRating([$user->getUsername() => 1]);
                        $imageFile = $form->get('image_path')->getData();

                        if ($imageFile and $repositoryGotSpace) {
                            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                            try {
                                $imageFile->move(
                                    $directoryPath ,
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                throw new \Exception('Failed to upload the image');
                            }
                            $yeti->setImagePath($directoryPath .'/'.$newFilename);
                        } else {
                            $yeti->setImagePath('/var/www/html/public/uploads/default/yeti.jpg');
                        }
                        $entityManager->persist($yeti);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_yetinder_index');
                    }
                } else {
                    $error = "špatné jméno";
                }

                $cesta = 'Form/main.html';
                return $this->render('default.html.twig', [
                'pathToMain' => $cesta,
                'yetiForm' => $form,
                'vykreslovatImg' => $repositoryGotSpace,
                'error' => $error
            ]);
            } else  {
                return $this->redirectToRoute('app_register');
            }
        }
    }
}