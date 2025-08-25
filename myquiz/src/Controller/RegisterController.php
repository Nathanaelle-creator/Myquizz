<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegisterUserTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $entity_manager,
        Mail $mail
    ): Response
    {
        //creation d'un new user (new object ) qui correspond a mon entité
        $user = new User();
        //IMPORTER le fichier form dans la vue
        $form = $this->createForm(RegisterUserTypeForm::class, $user);
        
         //ecoute la request (tous ce que utlisateur saisie)avant d'aller plus loin 
        $form->handleRequest($request);

        //si formulaire est soumis => verif si est soumi et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //  l'utilisateur comme non vérifié 
            $user->setIsVerified(false);

            //ce qu'on saisi dans le formulaire
            //dd($form->getData());
            //figer les donner BDO
            $user->setRoles(['ROLE_USER']);
            $entity_manager->persist($user);
               //enregistrer les donneés ->tu la push dans BDO
            $entity_manager->flush();
            
            //A: creation de url 
 //mon token n'est pas dans bdo normalment ??

            $Creationtoken = bin2hex(random_bytes(16)); 
            //met le token dans setconfirmation otken 
            $user->setConfirmationToken($Creationtoken); 
            //le fixe 
            $entity_manager->persist($user);
            //et le push 
            $entity_manager->flush();

            $verificationUrl = $this->generateUrl('app_verify', ['token' => $Creationtoken], UrlGeneratorInterface::ABSOLUTE_URL);

            //B:Envoyer un mail de confirmation (model pour envoyer enmail le mettre dans le controller )
            //$mail=new Mail();
            $vars=[
                'username'=>$user->getUsername(),
                'verifUrl' => $verificationUrl,
            ];
            $mail->send($user->getEmail(),$user->getUsername(),'Bienvenue sur My_Quizz','Mail/welcom.html.twig',$vars);
            // Rediriger
            return $this->redirectToRoute('app_login');  
        }
        // Rendre la vue avec le formulaire
        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

        //Faire la route pour la verification 
        #[Route('/verify/{token}', name: 'app_verify')]
        public function verify(string $token, EntityManagerInterface $entity_manager): Response
{
    // getrepository demande a la dbo DE trouver la le token dans user
    $user = $entity_manager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

    if ($user) {
        // Si un utilisateur a été trouvé avec ce token, le marquer comme vérifié
        $user->setIsVerified(true);
        //fixé
        $entity_manager->persist($user);
        //enregistrer dans dbo
        $entity_manager->flush();

        $this->addFlash('success', 'Votre compte a été confirmer . Vous pouvez maintenant vous jouer.');
        return $this->redirectToRoute('app_login');
    }

    // Si le token est invalide, afficher un message d'erreur
    $this->addFlash('error', 'Le lien de confirmation est invalide ou a expiré.');

    return $this->redirectToRoute('app_register');
}

}

//Notions
//USE je l'appel
//Namespace definie un repertoire
//injection de dependance =objectif une fonction peut fonction sans parametre 
//pour une fontion fonctionne on va lui injecter (dependre d'une fonction)

//Route pour la verification email