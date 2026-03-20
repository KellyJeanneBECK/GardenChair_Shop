<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'required'=>'true',
                'label'=>'First name',
                'attr'=>['class'=>'form form-control']
            ])
            ->add('lastName', null, [
                'required'=>'true',
                'label'=>'Last name',
                'attr'=>['class'=>'form form-control']
            ])
            ->add('phone', null, [
                'required'=>'true',
                'label'=>'Phone number',
                'attr'=>['class'=>'form form-control']
            ])
            ->add('adress', null, [
                'required'=>'true',
                'label'=>'Adress',
                'attr'=>['class'=>'form form-control']
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('city', EntityType::class, [
                'required'=>'true',
                'class' => City::class,
                'choice_label' => 'name',
                'label'=>'City',
                'attr'=>['class'=>'form form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}