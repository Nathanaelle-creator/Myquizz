<?php

namespace App\Controller;

use App\Class\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {   
        //envoye ses 2 ligne lorque quon voudrais utliser ou envoyer mail 
        //$mail= new Mail();
        //$mail->send('hell@gmail','ff','ff','ff');
        return $this->render('home/index.html.twig', [
            
        ]);
    }
}
