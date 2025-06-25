<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\InterventionTeam;
use App\Entity\Technician;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionTeamForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'type',
                \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,
                [
                    'choices' => [
                        'Intervention' => 'intervention',
                        'Montage' => 'montage',
                        'Démontage' => 'demontage',
                        'permanence' => 'permanence',
                        'visite de chantier' => 'visite_chantier',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'stardDate',
                \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'Date de début',
                    'html5' => true,
                    'attr' => ['class' => 'js-datepicker '],
                ]
            )
            ->add(
                'endDate',
                \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'Date de fin',
                    'html5' => true,
                    'attr' => ['class' => 'js-datepicker'],
                ]
            )
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'label' => 'Événement',
                'placeholder' => 'Sélectionnez un événement',
                'choice_label' => 'name',
                'choice_attr' => function (Event $event) {
                    return [
                        'data-start' => $event->getDateMontage()?->format('Y-m-d\\TH:i'),
                        'data-start-show' => $event->getDateStartShow()?->format('Y-m-d\\TH:i'),
                        'data-end-show' => $event->getDateEndSHOW()?->format('Y-m-d\\TH:i'),
                        'data-end' => $event->getDateEnd()?->format('Y-m-d\\TH:i'),
                    ];
                }
            ])
            ->add('technicians', EntityType::class, [
                'class' => Technician::class,
                'choice_label' => 'user.firstName',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionTeam::class,
        ]);
    }
}
