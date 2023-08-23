<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Team;
use App\Entity\Gender;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('phone', TextType::class, [
                'label' => 'N° de téléphone',
                'required' => false,
                ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
                ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'help' => 'Le mot de passe doit contenir au minimum 10 caractères avec au minimum 1 minuscule, 1 majuscule, 1 chiffre et un caractère spécial parmis @ ! ? * + - _ ~',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'autocomplete' => 'new-password'
                    ]
                ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN', 
                    'Administrateur client' => 'ROLE_ADMIN_CUSTOMER', 
                    'Team client' => 'ROLE_CUSTOMER',
                    'Utilisateur enregistré' => 'ROLE_IDENTIFIED'
                ],
                'label' => 'Role',
                'required' => true
            ])
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
