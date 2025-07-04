<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\SiteEvent;
use App\Form\ContactType;
use App\Form\EventType;
use App\Form\SiteEventType;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\GaleryPictureRepository;
use App\Repository\ProductRepository;
use App\Service\PlanningService;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        EventRepository $eventRepository,
        EventDetailRepository $eventDetails,
        ProductRepository $products,
        PlanningService $planningService
    ): Response {
        $orderCounts = $eventDetails->countOrdersByStatus(); 
        $event = new Event();
        $formEvent = $this->createForm(EventType::class, $event);
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $site = new SiteEvent();
        $formSite = $this->createForm(SiteEventType::class, $site);
        $data = $planningService->generateCalendarData();
        $json = json_encode($data, JSON_THROW_ON_ERROR); // plus sûr

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $eventRepository->findAll(),
            'eventDetails' => $eventDetails->findAll(),
            'products' => $products->findAll(),
            'orderCounts' => $orderCounts,
            'data' => $json ,
            'form' => $formEvent,
            'formContact' => $formContact,
            'formSite' => $formSite
            
        ]);
    }
    
}
