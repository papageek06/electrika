<?php

namespace App\Form;


use App\Entity\SiteEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\GaleryPicture;
use App\Entity\Contact;

class SiteEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du site',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Nom du site (ex: Palais des Festivals)'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Adresse complète'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ville (ex: Cannes)'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Code postal'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du site',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ajoutez une description (ex: accès, contraintes techniques...)',
                    'rows' => 4
                ]
            ])
            ->add('galeryPictures', EntityType::class, [
                'class' => GaleryPicture::class,
                'choice_label' => 'picture', 
                'multiple' => true,
                'expanded' => false, 
                'label' => 'Galerie Photos',
                'required' => false,
                'attr' => [
                    'class' => 'form-select mb-3',
                ],
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'choice_label' => 'firstName', 
                'multiple' => true,
                'expanded' => false,
                'label' => 'Contacts liés',
                'required' => false,
                'attr' => [
                    'class' => 'form-select mb-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteEvent::class,
        ]);
    }
}
