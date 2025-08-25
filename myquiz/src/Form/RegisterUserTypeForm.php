<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;



class RegisterUserTypeForm extends AbstractType
{

    //ICI NOUS AVONS LA FORME DU REGISTER JE PEUX AJOUTER OU SUPP DES ELEMENTS faire les placeholder  
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            
             ->add('username',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Enter your username'
                ]
            ])
            ->add('email',EmailType::class,[
                'attr'=>[
                    'placeholder'=>'Enter your email'
                ]
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type' => PasswordType::class,
                'constraints' => [new Length(['min' => 3])],
                'first_options'  => [
                    'label' => 'Enter your password', 
                    'attr'=>[
                    'placeholder'=>'Enter your password'
                    ],
                    //pour le mdp soit crypter
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => 'Confirm your password',
                    'attr'=>[
                    'placeholder'=>'Confirm your password'
                ]
                ],
                //fait pas lien entre entité et le champ que je te donne
                'mapped' => false,
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-sucess'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [new UniqueEntity([
                //je veux que email soit unique 
                'entityClass'=>User::class,
                'fields' => 'email'
            ])],
            'data_class' => User::class,
            
        ]);
    }
}
