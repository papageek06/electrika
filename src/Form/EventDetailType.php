<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventDetail;
use App\Entity\product;
use App\Entity\User;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('mouve', ChoiceType::class, [
            'choices' => [
                'Nouveau' => 'new',
                'Prêt' => 'pret',
                'Livré' => 'livrer',
                'Retour' => 'retour',
                'Annulé' => 'annuler',
            ],
            'label' => 'Type de mouvement',
            'attr' => ['class' => 'form-select'],
        ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
            ])
            ->add('product', EntityType::class, [
                'class' => product::class,
                'choice_label' => 'name',
            ])
            ->add('quantity')
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDetail::class,
        ]);
    }
}
