<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Connector;
use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\EventDetail;
use App\Entity\GaleryPicture;
use App\Entity\InterventionTeam;
use App\Entity\Product;
use App\Entity\SiteEvent;
use App\Entity\Technician;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Electrika');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('🏠 Retour au site', 'fas fa-home', 'app_home');

    yield MenuItem::section('📂 Gestion des entités');

    yield MenuItem::linkToCrud('Commandes', 'fas fa-file-invoice', Commande::class);
    yield MenuItem::linkToCrud('Produits', 'fas fa-box', Product::class);
    yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Category::class);
    yield MenuItem::linkToCrud('Connecteurs', 'fas fa-plug',Connector::class);
    yield MenuItem::linkToCrud('Événements', 'fas fa-calendar-alt',Event::class);
    yield MenuItem::linkToCrud('Détails Événement', 'fas fa-info-circle',EventDetail::class);
    yield MenuItem::linkToCrud('Sites Événement', 'fas fa-map-marker-alt',SiteEvent::class);
    yield MenuItem::linkToCrud('Absences', 'fas fa-user-slash',Absence::class);
    yield MenuItem::linkToCrud('Galerie', 'fas fa-images',GaleryPicture::class);
    yield MenuItem::linkToCrud('Contacts', 'fas fa-address-book',Contact::class);
    yield MenuItem::linkToCrud('Techniciens', 'fas fa-user-cog',Technician::class);
    yield MenuItem::linkToCrud('Interventions', 'fas fa-tools',InterventionTeam::class);
    yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users',User::class);
        
        

    }
}
