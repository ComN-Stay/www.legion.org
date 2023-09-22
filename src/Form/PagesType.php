<?php

namespace App\Form;

use App\Entity\Pages;
use App\Entity\Status;
use App\Entity\PagesTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
            ])
            ->add('fk_type', EntityType::class, [
                'label' => 'Type de page',
                'required' => true,
                'class' => PagesTypes::class,
                'choice_label' => 'name'
            ])
            ->add('meta_title', TextType::class, [
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
            ->add('fk_status', EntityType::class, [
                'label' => 'Statut',
                'required' => true,
                'class' => Status::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
