<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    private $security;
    private $currentUser;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->currentUser = $this->security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(is_null($builder->getData()->getId())) {
            $builder
                ->add('type', ChoiceType::class, [
                    'mapped' => false,
                    'choices' => [
                        'Sélectionner' => '',
                        'Compte personnel' => 'perso',
                        'Compte pour une association' => 'asso',
                        'Compte pour un éleveur' => 'pro'
                        ],
                    'label' => 'Type de compte',
                    'required' => is_null($builder->getData()->getId()) ? true : false,
                    ]);
                }
        if(!empty(array_intersect($this->currentUser->getRoles(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']))) {
            $builder
                ->add('fk_company', EntityType::class, [
                    'class' => Company::class,
                    'choice_label' => 'name',
                    'label' => 'Organisation',
                    'required' => false,
                    'attr' => [
                        'id' => 'fkCompany',
                    ],
                    'group_by' => 'fk_company_type.name'
                ]);
        }
        $builder
            ->add('fk_gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => 'name',
                'label' => 'Civilité',
                'required' => true,
                'attr' => [
                    'id' => 'fkGender',
                ]
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
                'empty_data' => ''
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
            ->add('job', TextType::class, [
                'label' => 'Poste',
                'required' => false,
            ])
            ->add('bo_access_auth', CheckboxType::class, [
                'label' => 'Autoriser l\'accès au back-office',
                'required' => false
                ])
            ->add('articles_auth', CheckboxType::class, [
                'label' => 'Autoriser la rédaction d\'articles',
                'required' => false
                ])
            ->add('adverts_auth', CheckboxType::class, [
                'label' => 'Autoriser la rédaction d\'annonces',
                'required' => false
                ])
            ->add('petitions_auth', CheckboxType::class, [
                'label' => 'Autoriser la proposition de pétitions',
                'required' => false
                ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En cochant cette case je reconnais avoir pris connaissance des conditions générales de Légion',
                'required' => true
                ])
            ->add('is_admin', CheckboxType::class, [
                'label' => 'Administrateur',
                'required' => false,
                'mapped' => false
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
