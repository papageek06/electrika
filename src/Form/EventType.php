<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\SiteEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')

            ->add('dateMontage', null, [
                'widget' => 'single_text',
            ])
            ->add('dateStartShow', null, [
                'widget' => 'single_text',
            ])
            ->add('dateEndSHOW', null, [
                'widget' => 'single_text',
            ])
            ->add('dateEnd', null, [
                'widget' => 'single_text',
            ])
            ->add('site', EntityType::class, [
                'class' => SiteEvent::class,
                'choice_label' => 'name',
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'choice_label' => 'lastName',
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
