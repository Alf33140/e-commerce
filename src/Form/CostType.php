<?php

namespace App\Form;

use App\Entity\Cost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city',null, [
                'required'=>'true',
                'label'=>'Nom de la ville',
                'attr'=>['class'=> 'form form-control', 'placeholder'=> 'Nom de la Ville'],
            ])
            ->add('Cost', null,[
                'required'=> 'true',
                'label'=> 'Frais de livraison',
                'attr'=>['class'=>'form form-filtered']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cost::class,
        ]);
    }
}
