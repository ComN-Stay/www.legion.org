<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Gender;

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
