<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
   public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield EmailField::new('email', 'Adresse email');

        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');

        yield ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Technicien' => 'ROLE_TECHNICIAN',
                'Client' => 'ROLE_CLIENT',
            ])
            ->allowMultipleChoices()
            ->renderExpanded(false);

        yield BooleanField::new('isVerified', 'Email vérifié');

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm();

        if ($pageName === Crud::PAGE_NEW) {
            yield TextField::new('plainPassword', 'Mot de passe')
                ->setFormType(PasswordType::class);
        }
    }
    
}
