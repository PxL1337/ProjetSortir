<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'All campuses',
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'All roles',
            ])
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'label' => 'Sort users by',
                'choices' => [
                    'Firstname' => 'firstname',
                    'Lastname' => 'lastname',
                    'Email' => 'email',
                    'Campus' => 'campus',
                    'Role' => 'role'],
                'placeholder' => 'Choose sorting option',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}

