<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required'=>'true',
                'label'=>'City name',
                'attr'=>['class'=>'form form-control', 'placeholder'=>'City']
            ])
            ->add('shippingCost', null, [
                'required'=>'true',
                'label'=>'Shipping cost',
                'attr'=>['class'=>'form form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}