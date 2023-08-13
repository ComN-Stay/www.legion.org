<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Tags;

class TagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du tag',
                'required' => true
            ])
            ->add('meta_name', TextType::class, [
                'label' => 'Meta name',
                'help' => 'Max 65 caractères',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 65,
                        'minMessage' => 'Le minimum de caractères requis est de {{ limit }}',
                        'maxMessage' => 'Votre Meta Name ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('meta_description', TextareaType::class, [
                'label' => 'Meta description',
                'help' => 'Max 175 caractères',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'max' => 175,
                        'minMessage' => 'Le minimum de caractères requis est de {{ limit }}',
                        'maxMessage' => 'Votre Meta Description ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('meta_keyword', TextType::class, [
                'label' => 'Mots clés',
                'help' => 'Max 10 mots séparés par des virgules',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tags::class,
        ]);
    }
}
