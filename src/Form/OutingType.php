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
    /*public function buildForm(FormBuilderInterface $builder, array $options, CampusRepository $campusRepository, CityRepository $cityRepository): void
    {

        $campusList = $campusRepository->findAll();
        $campusChoices = [];
        foreach ($campusList as $campus) {
            $campusChoices[$campus->getId()] = $campus->getNom();
        }

        $cityList = $cityRepository->findAll();
        $cityChoices = [];
        foreach ($cityList as $city) {
            $cityChoices[$city->getId()] = $city->getNom();
        }


        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class,[
                'input'=>'date',
            ])
            ->add('duree', TimeType::class,[
                'input'=>'interval',
                'with_seconds' => false,
            ])
            ->add('dateLimiteInscription', DateTimeType::class,[
                'input'=>'date',
            ])
            ->add('nbInscriptionMax', IntegerType::class)
            ->add('infosSortie',TextareaType::class)
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'Créée' => 'Créée',
                    'Ouverte' => 'Ouverte',
                    'Clôturée' => 'Clôturée',
                    'Activité en cours' => 'Activité en cours',
                    'Passée' => 'Passée',
                    'Annulée' => 'Annulée',
                ],
            ])
            ->add('Organizer')
            ->add('attendees')
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'choices' => $campusChoices,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un campus',
            ])
            ->add('place', TextType::class)
            ->add('rue', TextType::class,)
            ->add('longitude', NumberType::class, [
                'scale' => 8,
            ])
            ->add('lattitude', NumberType::class, [
                'scale' => 8,
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choices' => $cityChoices,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une ville',
            ])
        ;
    }*/

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
