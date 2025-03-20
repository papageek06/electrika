<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\GaleryPicture;
use App\Entity\SiteEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

class GaleryPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => 'picture',
                'mapped' => false, // ne lie pas directement le champ à la colonne picture de User
                'required' => true

                ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un événement',
                'required' => false, // Laisser facultatif si nécessaire
                'multiple' => true, // Un seul événement par image
                'expanded' => false , // Sélection sous forme de liste déroulante
                'by_reference' => false // Nécessaire pour les relations ManyToMany
            ])
            ->add('site', EntityType::class, [
                'class' => SiteEvent::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un site',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false // Nécessaire pour ManyToMany
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GaleryPicture::class,
        ]);
    }
}
