<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventRepository $eventRepository,EventDetailRepository $eventDetails, ProductRepository $products ): Response
    {
        $orderCounts = $eventDetails->countOrdersByStatus();
        $eventListDistinc = $eventRepository->findByEventDistinct();
        $eventList = $eventRepository->findAll();
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        
        foreach($eventList as $event) {
            $miss[] = [
                "title" => $event->getName() ,
                "start" => $event->getDateMontage()->format("Y-m-d"),
                "end" => $event->getDateEnd()->add(new DateInterval('P1D'))->format("Y-m-d"),
                "backgroundColor" => $event->getEventDetails('mouve'),
            ];
           
        }
        foreach($eventListDistinc as $eventList) {
            $miss[] = [
                // dd($eventList),
                "title" => $eventList['name'], 
                "start" => $eventList['date']->format("Y-m-d"),
                "end" => $eventList['date']->add(new DateInterval('P1D'))->format("Y-m-d"),
                "backgroundColor" => $eventList['mouve'] == 'livrer' ? 'green' : 'red',
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
            'form' => $form
        ]);
    }
}
