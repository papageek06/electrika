<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventRepository $events,EventDetailRepository $eventDetails, ProductRepository $products): Response
    {
        $orderCounts = $eventDetails->countOrdersByStatus();
        

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events->findAll(),
            'eventDetails' => $eventDetails->findAll(),
            'products' => $products->findAll(),
            'orderCounts' => $orderCounts,
        ]);
    }
}
