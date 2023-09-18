<?php

namespace App\Form;

use App\Entity\PagesTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PagesTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Type de page',
                'required' => true
            ])
            ->add('type', TextType::class, [
                'label' => 'Code type',
                'required' => true
            ])
            ->add('has_version', CheckboxType::class, [
                'label' => 'Suivi des versions',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PagesTypes::class,
        ]);
    }
}
