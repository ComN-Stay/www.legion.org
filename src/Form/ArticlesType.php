<?php

namespace App\Form;

use App\Entity\Tags;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Status;
use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('short_description', TextareaType::class, [
                'label' => 'Résumé',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
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
            ->add('meta_name', TextType::class, [
                'label' => 'Meta title',
                'required' => false
            ])
            ->add('meta_description', TextType::class, [
                'label' => 'Meta description',
                'required' => false
            ])
            ->add('meta_keywords', TextType::class, [
                'label' => 'Meta mots clés',
                'required' => false
            ])
            ->add('fk_team', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'fullname',
                'label' => 'Administrateur',
                'required' => false
            ])
            ->add('fk_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname',
                'label' => 'Utilisateur',
                'required' => false
            ])
            ->add('fk_status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label' => 'Statut',
                'required' => true
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'title',
                'label' => 'Tags',
                'required' => true,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
