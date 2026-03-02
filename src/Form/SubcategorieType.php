<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Subcategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubcategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('category', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',  // on remplace 'id' par "name' pour afficher le nom de la catégorie dans le select ala place du numero id de la categorie ds notre base
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subcategorie::class,
        ]);
    }
}
