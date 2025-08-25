<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\EmailUsertypeForm;
use App\Form\PasswordUsertypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            
        ]);
    }

 #[Route('/account/modify_password', name: 'app_account_modify_pwd')]
    public function password(Request $request,
    UserPasswordHasherInterface $PasswordHasher,
    EntityManagerInterface $entitymanager,
    Mail $mail): Response
    {
        //avoir le user encours
        /** @var \App\Entity\User $user */
        $user=$this->getUser();

    
        $form=$this->createForm(PasswordUsertypeForm::class,$user,[
            'passwordHasher'=>$PasswordHasher
        ]);
        //mettre a ecoute et fait quelquechosee (modifier)

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            //debuger 
            //dd($form->getData());
            if ($user->getConfirmationToken()) {
                $user->setConfirmationToken(null);  
        }
            //dd($user);
            $Creationtoken = bin2hex(random_bytes(16)); 
            //met le token dans setconfirmation otken 
            $user->setConfirmationToken($Creationtoken); 
            //le fixe 
            $entitymanager->persist($user);
            //et le push 
            $entitymanager->flush();
            

            $verificationUrl = $this->generateUrl('app_verify', ['token' => $Creationtoken], UrlGeneratorInterface::ABSOLUTE_URL);
            $vars = [
            'username'=>$user->getUsername(),
            'verifUrl' => $verificationUrl,
        ];

        $mail->send(
            $user->getEmail(),
            $user->getUsername(),
            'Votre mot de passe a bien été modifié',
            'Mail/modif.html.twig',
            $vars);
        }
       //dd('Mail envoyé');
        return $this->render('account/password.html.twig', [
            'modifyPwd'=>$form->createView()
        ]);
    }


        #[Route('/account/modify_email', name: 'app_account_modify_email')]
    public function email(Request $request,
    EntityManagerInterface $entitymanager,
    Mail $mail): Response
    {
        /** @var \App\Entity\User $user */
        //avoir le user encours
        $user=$this->getUser();

        $form=$this->createForm(EmailUsertypeForm::class,$user,[
            'email' => $user->getEmail(),
        ]);
        //mettre a ecoute et fait quelquechosee (modifier)

        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            //debuger 
            //dd($form->getData());
            $user->setEmail($form->get('plainEmail')->getData());
            $entitymanager->flush();
            return $this->redirectToRoute('app_account_modify_email');
        }
        return $this->render('account/email.html.twig', [
            'modifyEmail'=>$form->createView()
        ]);
    }
}
