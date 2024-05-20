<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\RatingHistory;
use App\Entity\User;
use App\Entity\Yetties;
use App\Form\YetiFormType;
use App\Repository\RatingHistoryRepository;
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
    public function Yetinder(YettiesRepository $yetiRepository): Response
    {
        if ($this->getUser()) {
            $cesta = 'YetinderPage/main.html';
            $RelevantYeti = $yetiRepository->findRelevant($this->getUser());
            return $this->render('default.html.twig', [
               'pathToMain' => $cesta,
               'yeti' => $RelevantYeti
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
                $repositoryGotSpace = true;

                // Ensure the directory exists
                if (!$filesystem->exists($directoryPath)) {
                    try {
                        chown('/var/www/html/public/uploads', 'www-data');
                        chgrp('/var/www/html/public/uploads', 'www-data');
                        $filesystem->mkdir($directoryPath, 0755);
                        chown($directoryPath, 'www-data');
                        chgrp($directoryPath, 'www-data');
                    } catch (\Exception $e) {
                        $repositoryGotSpace = false;
                    }
                }

                // Calculate directory size to avoid exceeding the limit
                if($repositoryGotSpace == true){
                    $directorySize = 0;
                    $finder->files()->in($directoryPath);
                    foreach ($finder as $file) {
                        $directorySize += $file->getSize();
                    }
                    //if for knowing, if there is still space
                    if ($directorySize < $maxDirectorySize) {
                        $repositoryGotSpace = true;
                    } else {
                        $repositoryGotSpace = false;
                    }
                }
                //saving everything
                if (!$yetiRepository->findOneByName($yeti->getName())) {
                     if ($form->isSubmitted() && $form->isValid()) {
                        $yeti->setDate(new \DateTime());
                        $yeti->setRating([$user->getUsername() => 0]);
                        $imageFile = $form->get('image_path')->getData();

                        if ($imageFile and $repositoryGotSpace) {
                            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                            try {
                                $imageFile->move(
                                    $directoryPath,
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                throw new \Exception('Failed to upload the image');
                            }
                            $yeti->setImagePath('/uploads/images/'.$newFilename);
                        } else {
                            $yeti->setImagePath('/uploads/default/yeti.jpg');
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
    #[Route ('/yetinder/up', name: 'app_UpVote', methods: ['POST'])]
    public function upVoteForm(Request $request, EntityManagerInterface $entityManager, YettiesRepository $yetiRepository){
        $user = $this->getUser();
        //přidání hodnocení
        $yeti = $request->request->get('yeti');
        $OneYeti = $yetiRepository->findOneByName($yeti);
        $OneYeti->addRating([$user->getUsername() => 1]);
        // historie upvotů

        $entityManager->persist($OneYeti);
        $entityManager->flush();
        return $this->redirectToRoute('app_yetinder_index');
    }
    #[Route ('/yetinder/down', name: 'app_DownVote', methods: ['POST'])]
    public function downVoteForm(Request $request, EntityManagerInterface $entityManager, YettiesRepository $yetiRepository, RatingHistoryRepository $ratingHistoryRepository){
        $user = $this->getUser();
        $newDown = new RatingHistory();
        //přidání hodnocení
        $yeti = $request->request->get('yeti');
        $OneYeti = $yetiRepository->findOneByName($yeti);
        $OneYeti->addRating([$user->getUsername() => -1]);
        // historie downvotů
        if($ratingHistoryRepository->findOneBy(['Record[0]' => date('Y-m-d')])){
            $Date = $ratingHistoryRepository->findOneBy(['Record[0]' => date('Y-m-d')]);
            $ratings = $Date->getRatings();
            $ratings[2][date('Y-m-d')] += 1;
            $Date->addRecord()($ratings);
        } else {
            $Date = New RatingHistory();
            $Date->setRecord([date('Y-m-d') => 0, 1]);
        }

        $entityManager->persist($OneYeti);
        $entityManager->persist($Date);
        $entityManager->flush();
        return $this->redirectToRoute('app_yetinder_index');
    }
}