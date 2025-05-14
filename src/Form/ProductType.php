<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Connector;
use App\Entity\Product;
use App\Entity\ProductConnectors;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Affiche le nom de la catégorie dans la liste déroulante
                'placeholder' => 'Sélectionner une catégorie',
                'required' => true,
            ])
            ->add('stockInitial')
            ->add('stock')
            ->add('hs')
            ->add('lost')
            ->add('picture', FileType::class, [
                'label' => 'Avatar',
                'mapped' => false, // ne lie pas directement le champ à la colonne picture de User
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG.',
                    ])
                ]
            ])
            ->add('productConnectors', CollectionType::class, [
                'entry_type' => ProductConnectorForm::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
