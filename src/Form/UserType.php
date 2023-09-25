<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Compte personnel' => 'perso',
                    'Compte pour une association' => 'asso',
                    'Compte pour un éleveur' => 'pro'
                    ],
                'label' => 'Type de compte',
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])
            ->add('fk_gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => 'name',
                'label' => 'Civilité',
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true
                ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'autocomplete' => 'new-password'
                        ]
                    ],
                'required' => is_null($builder->getData()->getId()) ? true : false,
                'first_options'  => [
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe'
                ],
                'help' => 'Le mot de passe doit contenir au minimum 10 caractères avec au minimum 1 minuscule, 1 majuscule, 1 chiffre et un caractère spécial parmis @ ! ? * + - _ ~',
            ])
            ->add('picture', FileType::class, [
                'data_class' => null,
                'label' => 'Avatar',
                'required' => false,
                'help' => 'Fichier jpg, jpeg, png ou webp ne dépassant pas 1 Mo',
                'constraints' => [
                    new File([ 
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Votre fichier ne doit pas dépasser les 1 M0',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => "Merci de télécharger une photo valide.",
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En cochant cette case je reconnais avoir pris connaissance des conditions générales de Légion',
                'required' => true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
