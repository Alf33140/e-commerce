<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Subcategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('descriptionproduct')
            ->add('Price')
            ->add('stock')
            ->add('image', FileType::class, [
                'label'=> 'Image du Produit',
                'mapped' => false,
                'required'=> false,
                'constraints' => [
                new File(
                    maxSize: '1024k',
                    mimeTypes: [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    maxSizeMessage: 'La taille du fichier ne doit pas dépasser 1 Mo.',
                    mimeTypesMessage: 'Veuillez choisir un fichier de type image (JPEG, PNG, GIF)!!',
                ),
            ],
        ])
        
            ->add('subcategory', EntityType::class, [
                'class' => Subcategorie::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
