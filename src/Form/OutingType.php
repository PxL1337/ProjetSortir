<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Outing;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public CampusRepository $campusRepository;
    public CityRepository $cityRepository;

    public function __construct(CampusRepository $campusRepository, CityRepository $cityRepository)
    {
        $this->campusRepository = $campusRepository;
        $this->cityRepository = $cityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $campusList = $this->campusRepository->findAll();
        $campusChoices = [];
        foreach ($campusList as $campus) {
            $campusChoices[$campus->getId()] = $campus->getNom();
        }

        $cityList = $this->cityRepository->findAll();
        $cityChoices = [];
        foreach ($cityList as $city) {
            $cityChoices[$city->getId()] = $city->getNom();
        }


        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'input' => 'datetime',
            ])
            ->add('duree', TimeType::class, [

                'with_seconds' => false,
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'input' => 'datetime',
            ])
            ->add('nbInscriptionMax', IntegerType::class)
            ->add(
                'infosSortie',
                TextareaType::class,
                array('attr' => array(
                    'class' => 'form-text-area',
                    'rows' => '4',
                    'maxlength' => '150',
                    'overflow' => 'hidden'))
            )
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Créée' => 'Créée',
                    'Ouverte' => 'Ouverte',
                    'Clôturée' => 'Clôturée',
                    'Activité en cours' => 'Activité en cours',
                    'Passée' => 'Passée',
                    'Annulée' => 'Annulée',
                ],
            ])
            /*
             ->add('Organizer')
             ->add('attendees')*/
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choices' => $campusList,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un campus',
            ])/*
            ->add('place')
            ->add('rue')
            ->add('longitude', NumberType::class, [
                'scale' => 8,
            ])
            ->add('lattitude', NumberType::class, [
                'scale' => 8,
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choices' => $cityList,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une ville',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
