<?php

namespace App\Form;

use App\Entity\Pages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('type', ChoiceType::class, [
                'label' => 'Type de page',
                'required' => true,
                'choices' => [
                    'Mentions légales' => 'ml',
                    'Conditions générales d\'utilisation' => 'CGU',
                    'Conditions générales de vente' => 'CGV',
                    'Politique de protection des données' => 'RGPD',
                    'Pages du site' => 'Pages',
                    'Block texte' => 'block'
                ]
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
