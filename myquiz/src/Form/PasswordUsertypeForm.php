<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUsertypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('actuelPassword',PasswordType::class,[
                'label'=>"Votre password actuel",
                'attr'=>[
                    'placeholder'=>'Indiquez  votre  password'
                    ],
                'mapped' => false,
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type' => PasswordType::class,
                'constraints' => [new Length(['min' => 3])],
                'first_options'  => [
                    'label' => 'Entre votre Password', 
                    'attr'=>[
                    'placeholder'=>'Choisissez  votre new  password'
                    ],
                    //pour le mdp soit crypter
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => 'Confirmer votre new  Password',
                    'attr'=>[
                    'placeholder'=>'Confirmer votre new  password'
                ]
                ],
                //fait pas lien entre entité et le champ que je te donne
                'mapped' => false,
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>[
                    'label'=>'Mettre à jour votre password',
                    'class'=>'btn btn-sucess'
                ]
            ])
            //je veux ecouter au submit 
            ->addEventListener(FormEvents::SUBMIT,function(FormEvent $event){
                //recup le password saisi (les elements dans notre form)
                $form=$event->getForm();
                //recup les info de user
                $user=$form->getConfig()->getOptions()['data'];
                $passwordHasher=$form->getConfig()->getOptions()['passwordHasher'];
                //1 recueper le mdp sais par utlisatereur
                $isValid=$passwordHasher->isPasswordValid(
                    $user,
                    $form->get('actuelPassword')->getData()
                );
                 if(!$isValid){
                    $form->get('actuelPassword')->addError(new FormError('votre mdp actuel n est pas valide'));
                 }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher'=>null
        ]);
    }
}
