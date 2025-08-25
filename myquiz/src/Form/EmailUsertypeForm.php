<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class EmailUsertypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajouter l'email actuel (non mappé)
        $builder
            ->add('actuelemail', EmailType::class, [
                'label' => "Votre email actuel",
                'attr' => [
                    'placeholder' => 'Indiquez votre email actuel',
                ],
                'mapped' => false,  // Ce champ n'est pas mappé à l'entité User
            ])
            // Ajouter le champ "plainEmail" avec la répétition
            ->add('plainEmail', RepeatedType::class, [
                'type' => EmailType::class,
                'constraints' => [new Length(['min' => 3])],
                'first_options' => [
                    'label' => 'Entrez votre nouvel email',
                    'attr' => [
                        'placeholder' => 'Choisissez votre nouvel email',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouvel email',
                    'attr' => [
                        'placeholder' => 'Confirmez votre nouvel email',
                    ],
                ],
                'mapped' => false,  // Ce champ n'est pas mappé à l'entité User
            ])
            // Ajouter le bouton de soumission
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success',
                ]
            ])
            // Écouter l'événement "submit"
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                $form = $event->getForm();
                $user = $form->getData();  // Récupérer l'objet User lié au formulaire

                // Vérifier que l'email actuel correspond à celui de l'utilisateur
                $actuelEmail = $form->get('actuelemail')->getData();
                if ($actuelEmail !== $user->getEmail()) {
                    $form->get('actuelemail')->addError(new FormError('Votre email actuel n\'est pas valide.'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,  
        ]);
        $resolver->setDefined('email');
    }
}
