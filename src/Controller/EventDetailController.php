<?php

namespace App\Controller;

use App\Entity\EventDetail;
use App\Form\EventDetailType;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('eventdetail')]
final class EventDetailController extends AbstractController
{
    #[Route(name: 'app_event_detail_index', methods: ['GET', 'POST'])]
    public function index(EventRepository $eventRepository, Request $request , EventDetailRepository $eventDetailRepository): Response
    {
        // -----------------------------------------filtrage sous condition------------------------------------------
        $event = $request->request->get('eventSelected');
        $status = $request->request->get('statusSelected');
        $order = $request->request->get('orderSelected');
        $listEvents=$eventRepository->findByDistinct();

        
       
        $events = [];
        
        if (!empty($status) && empty($event)) {  
            $this->addFlash('success', 'Filtrage status appliqué avec succès !');
            $events = $eventDetailRepository->findByStatus($status, $order);
            
        } else if(!empty($event) && empty($status)) {
            $this->addFlash('success', 'Filtrage event appliqué avec succès !');
            $events = $eventDetailRepository->findByEvent($event, $order);
        }else if (!empty($event) && !empty($status)) {
            $this->addFlash('success', 'Filtrage event et status appliqué avec succès !');
            $events = $eventDetailRepository->findByEventStatus($event, $status , $order);
        }
        else {

            $events = $eventDetailRepository->findAll();
        }

        
        return $this->render('event_detail/index.html.twig', [
            'event_details' => $events,
            'listEvents' => $listEvents
        ]);
        

    }

    #[Route('/new', name: 'app_event_detail_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventDetail = new EventDetail();
        $form = $this->createForm(EventDetailType::class, $eventDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventDetail);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_detail/new.html.twig', [
            'event_detail' => $eventDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_detail_show', methods: ['GET'])]
    public function show(EventDetail $eventDetail): Response
    {
        return $this->render('event_detail/show.html.twig', [
            'event_detail' => $eventDetail,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_event_detail_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EventDetail $eventDetail, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    { 
         
        $form = $this->createForm(EventDetailType::class, $eventDetail);
        $form->handleRequest($request);
      

        if ($form->isSubmitted() && $form->isValid()) {
            
            if($form->getData()->getMouve() == 'retour'){

                $product = $productRepository->find($form->getData()->getProduct()->getId());
                $product->setStock($product->getStock() - $form->getData()->getQuantity());
                $entityManager->persist($product);

            } else if ($form->getData()->getMouve() == 'livrer') {
                $product = $productRepository->find($form->getData()->getProduct()->getId());
                $product->setStock($product->getStock() - $form->getData()->getQuantity());
                $entityManager->persist($product);
            }else if ($form->getData()->getMouve() == 'annuler') {
                $eventDetail->setQuantity(0);
        
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_detail/edit.html.twig', [
            'event_detail' => $eventDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_event_detail_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EventDetail $eventDetail, EntityManagerInterface $entityManager): Response
    {
        
            
            $entityManager->remove($eventDetail);
            $entityManager->flush();
        
        $this->addFlash('success', 'Suppression effectuée avec succès !');

        return $this->redirectToRoute('app_event_detail_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/event-detail/update-all', name: 'app_event_detail_update_all', methods: ['POST'])]
    public function updateAll(Request $request, EntityManagerInterface $entityManager, EventDetailRepository $eventDetailRepository): Response
    {
        
        
        $statuses = $request->get('statuses',[]);
        $quantities = $request->get('quantities',[]);
        
        dd($statuses);
        foreach ($statuses as $id => $status) {
            $eventDetail = $eventDetailRepository->find($id);
            if ($eventDetail) {
                $eventDetail->setMouve($status);
                $eventDetail->setDate(new \DateTime());
            }
        }

        foreach ($quantities as $id => $quantity) {
            $eventDetail = $eventDetailRepository->find($id);
            if ($eventDetail) {
                $eventDetail->setQuantity((int) $quantity);
            }
        }

        $entityManager->flush();
        $this->addFlash('success', 'Mises à jour enregistrées avec succès.');

        return $this->redirectToRoute('app_event_detail_index');
    }
    #[Route('/app_event_upgrade/{id}', name: 'app_event_upgrade')]
    public function upgrade(Request $request ,EventDetailRepository $eventDetailRepository , int $id ,EventRepository $eventRepository , EntityManagerInterface $entityManager ): Response
    {
        $event = $eventRepository->find($id);
        $status = $request->query->get('status');
        

        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }
        $eventDetails = $eventDetailRepository->findBy(['event' => $event]);
      
        dump($status);
        
        foreach ($eventDetails as $eventDetail) {

            //  dd($eventDetail->getMouve());
            if ($status == 'bp' && $eventDetail->getMouve() === 'new') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event); 
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('bp'); 
                $entityManager->persist($newDetail);
                $entityManager->flush();
            } else if ($status == 'bl' && $eventDetail->getMouve() === 'bp') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event); 
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());              
                $newDetail->setMouve('bl'); 
                $entityManager->persist($newDetail);
                $entityManager->flush();
            } else if ($status == 'br' && $eventDetail->getMouve() === 'bl') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event); 
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('br');
                $entityManager->persist($newDetail);
                $entityManager->flush();
            }
            else if ($status == 'bf' && $eventDetail->getMouve() === 'bl') {
                $newDetail = new EventDetail();
                $newDetail->setUser($eventDetail->getUser());
                $newDetail->setProduct($eventDetail->getProduct());
                $newDetail->setEvent($event); 
                $newDetail->setQuantity($eventDetail->getQuantity());
                $newDetail->setDate(new \DateTime());
                $newDetail->setMouve('bf');
                $entityManager->persist($newDetail);
                $entityManager->flush();
             }

            

        }
    
        
        return $this->redirectToRoute('app_event_show', ['id' => $id]);

    }




}
