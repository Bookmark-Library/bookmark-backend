<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,
            [
                'label' => "Courriel"
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôle(s)',
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],
            ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // On récupère le form depuis l'event (pour travailler avec)
                $form = $event->getForm();
                // On récupère le user mappé sur le form depuis l'event
                $user = $event->getData();

                // Si user existant, il a id non null
                if ($user->getId() !== null) {
                    // Edit
                    $form->add('password', PasswordType::class, [
                        'label' => 'Mot de passe',
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ],
                        'constraints' => [
                            new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-+]).{8,}$/', 
                            "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                        ), 
                        ],

                    ]);
                } else {
                    // New
                    $form->add('password', PasswordType::class, [
                        'label' => 'Mot de passe',
                        'empty_data' => '',
                        // On déplace les contraintes de l'entité vers le form d'ajout
                        'constraints' => [
                            new NotBlank(),
                            new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-+]).{8,}$/', 
                            "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                        ), 
                           
                        ],

                    ]);
                }
            })

            ->add('alias', TextType::class)
            ->add('avatar', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
