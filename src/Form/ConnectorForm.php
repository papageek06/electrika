<?php

namespace App\Form;

use App\Entity\Connector;
use App\Entity\product;
use App\Entity\SiteEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('power')
            ->add('in_out')
            ->add('phase_type')
            ->add('product', EntityType::class, [
                'class' => product::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('site', EntityType::class, [
                'class' => SiteEvent::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Connector::class,
        ]);
    }
}
