<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required'=>false,
                'attr'=> [
                    'placeholder' => 'Search'
                ]
            ])
            ->add('campus', EntityType::class, [
                'label'=>'Campus',
                'required'=>false,
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'expanded'=>false,
                'multiple'=>true,
                'placeholder' => 'Tous les campus',
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Between',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('dateMax', DateType::class, [
                'label' => 'and',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
