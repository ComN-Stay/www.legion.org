<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Company;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CompanyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la structure',
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'dir' => 'ltr',
                    'spellcheck' => 'false',
                    'autocorrect' => 'off',
                    'autocomplete' => 'off',
                    'autocapitalize' => 'off',
                    'aria-controls' => 'autoComplete_list', 
                    'aria-autocomplete' => 'both'
                ]
            ])
            ->add('additional_address', TextType::class, [
                'label' => 'Complément d\'adresse',
                'required' => false
            ])
            ->add('zip_code', TextType::class, [
                'label' => 'Code postal',
                'required' => true
            ])
            ->add('town' , TextType::class, [
                'label' => 'Ville',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true
                ])
            ->add('phone', TextType::class, [
                'label' => 'N° de téléphone',
                'required' => false,
                ])
            ->add('short_description', TextareaType::class, [
                'label' => 'Résumé',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('facebook', TextType::class, [
                'label' => 'Facebook',
                'required' => false,
                ])
            ->add('tweeter', TextType::class, [
                'label' => 'Tweeter',
                'required' => false,
                ])
            ->add('instagram', TextType::class, [
                'label' => 'instagram',
                'required' => false,
                ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => false,
                ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => false,
                ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Activé' => true, 
                    'Désactivé' => false
                    ],
                'label' => 'Statut',
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])
            ->add('logo', FileType::class, [
                'data_class' => null,
                'label' => 'Logo',
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
                        'mimeTypesMessage' => "Merci de télécharger une image valide.",
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En cochant cette case je reconnais avoir pris connaissance des conditions générales de Légion',
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])
            ->add('type_id', IntegerType::class, [
                'mapped' => false,
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])
            ->add('userToken', TextType::class, [
                'mapped' => false,
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
