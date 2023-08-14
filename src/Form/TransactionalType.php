<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Transactional;

class TransactionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('template', TextType::class, [
                'label' => 'Nom du template',
                'required' => true,
                'help' => 'Pas d\'espaces d\'accents ou de caractères spéciaux (ex. nom_du_template)'
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet du mail',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true
                ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu du mail',
                'required' => false,
                'attr' => [
                    'class' => 'tinymce',
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transactional::class,
        ]);
    }
}
