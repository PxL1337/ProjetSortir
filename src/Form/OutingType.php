<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Outing;
use App\Entity\Place;
use App\Form\DataTransformer\DateTimeTransformer;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
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
    public PlaceRepository $placeRepository;

    public function __construct(CampusRepository $campusRepository, CityRepository $cityRepository,PlaceRepository $placeRepository)
    {
        $this->campusRepository = $campusRepository;
        $this->cityRepository = $cityRepository;
        $this->placeRepository = $placeRepository;
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
        $placeList = $this->placeRepository->findAll();
        $placeChoices = [];
        foreach ($placeList as $place) {
            $placeChoices[$place->getId()] = $place->getNom();
        }


        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'input' => 'datetime',
                'html5'=>true,
                'widget'=> 'single_text'
            ])
            ->add('duree', TimeType::class, [
                'html5'=>true,
                'widget'=> 'single_text',
                'with_seconds' => false,
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'html5'=>true,
                'widget'=> 'single_text'
            ])
            ->add('nbInscriptionMax', IntegerType::class)
            ->add(
                'infosSortie',
                TextareaType::class,
                array('attr' => array(
                    'class' => 'form-text-area',
                    'rows' => '4',
                    'maxlength' => '255',
                    'overflow' => 'hidden'))
            )
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une ville',
                'mapped' => false,
                'required' => false
            ])

            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choices' => $placeList,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un lieu',
            ])
        ;

        $builder->get('duree')
            ->addModelTransformer(new DateTimeTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
