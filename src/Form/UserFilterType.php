<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Tous les campus',
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Tous les rôles',
            ])
            ->add('sort', ChoiceType::class, [
                'choices'  => [
                    'Prénom' => 'firstname',
                    'Nom' => 'lastname',
                    'Email' => 'email',
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Filtrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}

