<?php

namespace App\Form;

// All imports
use App\Entity\Stations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HistoricalStationSelectType extends AbstractType {

    public function __construct(private EntityManagerInterface $entityManager) {}
    /**
     * Builds a form that asks for a station_name
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $choices = $this->getChoices();
        $builder
            ->add('stationName', ChoiceType::class, [
                'choices' => [
                    'Choose' => 'No station selected',
                    'choices' => $choices
                    ],
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    private function getChoices():array
    {
        $stations = $this->entityManager->getRepository(Stations::class)->findAll();
        $stationNames = [];
        foreach ($stations as $station) {
            $stationName = $station->getStationName();
            $stationNames[$stationName] = $stationName;
        }
        return $stationNames;
    }
}