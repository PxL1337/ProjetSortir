<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\SearchData;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserSearchType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder->add('input', TextType::class, [
'required' => false,
'attr' => [
'placeholder' => 'Recherche des utilisateurs via un mot clé',
'id' => 'form_search_user'
]
]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
'data_class' => SearchData::class,
'method' => 'GET',
'csrf_protection' => false
]);
}
}
