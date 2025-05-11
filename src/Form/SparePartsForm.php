<?php

namespace App\Form;

use App\Entity\product;
use App\Entity\SpareParts;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SparePartsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('reference')
            ->add('description')
            ->add('quantity')
            ->add('picture')
            ->add('product', EntityType::class, [
                'class' => product::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SpareParts::class,
        ]);
    }
}
