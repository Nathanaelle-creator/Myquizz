<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;
use Twig\Environment;

class Mail{


     private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    public function send($to_email,$to_name,$subject,$template,$vars=null)
    {
    //recup template
    //$content = $this->twig->render('Mail/' . $template, $vars);
    //$content=file_get_contents(dirname(__DIR__).'/Mail/'.$template);
    $content = $this->twig->render($template, $vars);

    $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'],$_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);
    //recup des variable facult
        if($vars){
            foreach($vars as $key=>$var){
                $content=str_replace('{'.$key.'}',$var,$content);
            }
        }
        $body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "latouchenathanaelle@gmail.com",
                'Name' => 'my_quizz'
            ],
            'To' => [
                [
                    'Email' => $to_email,
                    'Name' => $to_name
                ]
            ],
            'Subject' => $subject,
            'TextPart' => "Greetings from Mailjet!",
            'HTMLPart' => $content
        ]
    ]
];
    $mj->post(Resources::$Email, ['body' => $body]);
    }
}