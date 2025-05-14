<?php

namespace App\Form;

use App\Entity\ProductConnector;
use App\Entity\Connector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductConnectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
            ])
            ->add('plugDirection', ChoiceType::class, [
                'choices' => [
                    'Alim' => 'alim',
                    'Sortie' => 'sortie',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Direction'
            ])
            ->add('connector', EntityType::class, [
                'class' => Connector::class,
                'choice_label' => fn(Connector $c) => sprintf('%dA - %s', $c->getPower(), $c->getType()),
                'label' => 'Connecteur',
                'placeholder' => 'Choisir un connecteur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductConnector::class,
        ]);
    }
}
