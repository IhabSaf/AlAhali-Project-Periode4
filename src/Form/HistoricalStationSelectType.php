<?php

namespace App\Form;

// All imports
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HistoricalStationSelectType extends AbstractType {
    /**
     * Builds a form that asks for a station_name
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('stationName', TextType::class, ['required' => true])
            ->add('Submit', SubmitType::class)
        ;
    }
}