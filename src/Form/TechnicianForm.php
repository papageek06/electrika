<?php

namespace App\Form;

use App\Entity\InterventionTeam;
use App\Entity\Technician;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnicianForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('hireDate',
                                        \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'date'],
                ])
            ->add('specialities')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
            ])
            ->add('interventionTeams', EntityType::class, [
                'class' => InterventionTeam::class,
                'choice_label' => 'event',
                'multiple' => true,
                'expanded' => true, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Technician::class,
        ]);
    }
}
