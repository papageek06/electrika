<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Technician;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbsenceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('startDate',
                \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'js-datepicker'],
                ]
            )
            ->add('endDate',
                \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'js-datepicker'],
                ]
            )
            ->add('comment', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Commentaire (facultatif)',
                    'rows' => 3,
                ],
            ]
            )
            ->add('technicians', EntityType::class, [
                'class' => Technician::class,
                'choice_label' => 'user.firstName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
