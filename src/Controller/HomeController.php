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
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EventRepository $eventRepository,EventDetailRepository $eventDetails, ProductRepository $products ): Response
    {
        $miss = [];
        $orderCounts = $eventDetails->countOrdersByStatus();
        $eventListDistinc = $eventRepository->findByEventDistinct();
        $eventList = $eventRepository->findAll();
        $event = new Event();
        $formEvent = $this->createForm(EventType::class, $event);
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $site = new SiteEvent();
        $formSite = $this->createForm(SiteEventType::class, $site);

        
        foreach($eventList as $event) {
            $miss[] = [
                "title" => $event->getName() ,
                "start" => $event->getDateMontage()->format("Y-m-d"),
                "end" => $event->getDateEnd()->add(new DateInterval('P1D'))->format("Y-m-d"),
                "backgroundColor" => 'grey',
                
            ];
           
        }
        foreach($eventListDistinc as $eventList) {
            $backgroundColor = '';
            if ($eventList['mouve'] == 'livrer') {
                $backgroundColor = 'green';
            } else if ($eventList['mouve'] == 'preparer') {
                $backgroundColor = 'orange';
            } else if ($eventList['mouve'] == 'new') {
                $backgroundColor = 'bleu';
            } else if ($eventList['mouve'] == 'retour') {
                $backgroundColor = 'red';
            }
            $miss[] = [
                
                "title" => $eventList['name'], 
                "start" => $eventList['date']->format("Y-m-d"),
                "end" => $eventList['date']->add(new DateInterval('P1D'))->format("Y-m-d"),
                "backgroundColor" => $backgroundColor,
                
            ];
           
        }
        

       

        $data = json_encode($miss);

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $eventRepository->findAll(),
            'eventDetails' => $eventDetails->findAll(),
            'products' => $products->findAll(),
            'orderCounts' => $orderCounts,
            'data' => $data,
            'form' => $formEvent,
            'formContact' => $formContact,
            'formSite' => $formSite
        ]);
    }
}
