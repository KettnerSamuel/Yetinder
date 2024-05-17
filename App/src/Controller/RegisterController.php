<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class RegisterController extends AbstractController
{

    #[Route ('/registerForm', name: 'app_registerForm', methods: ['POST'])]
    public function registerForm(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils){
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_register');
        }
        
        return $this->render('default.html.twig', [
            'registrationForm' => $form,
            'pathToMain' => 'registration/main.html',
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => 'špatně zadané jméno/heslo'
        ]);
    }

    #[Route('/account', name: 'app_register')]
    public function register(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $form = $this->createForm(RegistrationFormType::class);

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_index');
        } else  {
            return $this->render('default.html.twig', [
                'registrationForm' => $form,
                'pathToMain' => 'registration/main.html',
                'last_username' => $lastUsername,
                'error' => $error
            ]);
        }
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
