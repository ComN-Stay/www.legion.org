<?php

namespace App\Form;

use App\Entity\Adverts;
use App\Entity\PetsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'required' => true
            ])
            ->add('short_description', TextareaType::class, [
                'label' => 'Résumé de l\'anonce',
                'required' => true,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('description', TextareaType::class, [
                'label' => 'Texte de l\'annonce',
                'required' => true,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
            ->add('lof', ChoiceType::class, [
                'choices' => [
                    'Oui' => true, 
                    'Non' => false
                    ],
                'label' => 'LOF',
                'required' => false
            ])
            ->add('lof_number', IntegerType::class, [
                'label' => 'N° LOF',
                'required' => false
            ])
            ->add('lof_identifier', IntegerType::class, [
                'label' => 'Identifiant LOF',
                'required' => false
            ])
            ->add('lof_father_name', TextType::class, [
                'label' => 'Nom du Père',
                'required' => false
            ])
            ->add('lof_father_identifier', IntegerType::class, [
                'label' => 'Identifiant LOF du père',
                'required' => false
            ])
            ->add('lof_mother_name', TextType::class, [
                'label' => 'Nom de la mère',
                'required' => true
            ])
            ->add('lof_mother_identifier', IntegerType::class, [
                'label' => 'Identifiant LOF de la mère',
                'required' => false
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'animal',
                'required' => true
            ])
            ->add('birth_date')
            ->add('identified', ChoiceType::class, [
                'choices' => [
                    'Oui' => true, 
                    'Non' => false
                    ],
                'label' => 'Tatoué / pucé',
                'required' => false
            ])
            ->add('vaccinated', ChoiceType::class, [
                'choices' => [
                    'Oui' => true, 
                    'Non' => false
                    ],
                'label' => 'Vacciné',
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'required' => false
            ])
            ->add('is_pro', ChoiceType::class, [
                'choices' => [
                    'Oui' => true, 
                    'Non' => false
                    ],
                'label' => 'Annonce pro',
                'required' => false
            ])
            ->add('fk_pets_type', EntityType::class, [
                'class' => PetsType::class,
                'choice_label' => 'name',
                'label' => 'Type d\'animal',
                'required' => true
            ])
            ->add('fk_company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'label' => 'Editeur de l\'annonce',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adverts::class,
        ]);
    }
}
